<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Events\ProjectCreated;
use App\Mail\ProjectReceived;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::where('client_id', $request->user()->id)
            ->with(['freelancer', 'categories'])
            ->latest()
            ->get();

        return view('client.projects.index', compact('projects'));
    }

    public function create(): View
    {
        $categories = ProjectCategory::whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('client.projects.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['required', 'string'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:project_categories,id'],
            'files'        => ['nullable', 'array'],
            'files.*'      => ['file', 'max:10240'],
        ]);

        $project = Project::create([
            'client_id'   => $request->user()->id,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'status'      => 'pending',
        ]);

        if (!empty($validated['category_ids'])) {
            $project->categories()->sync($validated['category_ids']);
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('projects/' . $project->id, 'public');
                ProjectFile::create([
                    'project_id' => $project->id,
                    'file_path'  => $path,
                ]);
            }
        }

        ProjectCreated::dispatch($project->load('client'));

        // Send confirmation email to the client
        try {
            Mail::to($request->user()->email)->send(new ProjectReceived($project));
        } catch (\Throwable) {
            // Mail not configured — silently skip
        }

        // Auto-convert any open leads matching this client's email
        Lead::where('email', $request->user()->email)
            ->whereIn('status', ['new', 'contacted'])
            ->whereNull('converted_user_id')
            ->update([
                'status'            => 'converted',
                'converted_user_id' => $request->user()->id,
            ]);

        return redirect()->route('client.projects.index')
            ->with('success', "We've received your project request! Our team will review it and get back to you shortly.");
    }

    public function show(Request $request, Project $project): View
    {
        abort_unless($project->client_id === $request->user()->id, 403);

        $project->load(['freelancer', 'files', 'categories']);

        return view('client.projects.show', compact('project'));
    }
}
