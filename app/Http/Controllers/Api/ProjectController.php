<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Events\ProjectCreated;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Project::with(['client:id,name', 'freelancer:id,name', 'categories:id,name']);

        if ($user->role->value === 'client') {
            $query->where('client_id', $user->id);
        } elseif ($user->role->value === 'freelancer') {
            $query->where('freelancer_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate(15);

        return response()->json($projects);
    }

    public function show(Request $request, Project $project)
    {
        $user = $request->user();

        if ($user->role->value === 'client' && $project->client_id !== $user->id) {
            abort(403);
        }
        if ($user->role->value === 'freelancer' && $project->freelancer_id !== $user->id) {
            abort(403);
        }

        $project->load([
            'client:id,name,email',
            'freelancer:id,name,email',
            'categories:id,name',
            'files',
            'invoice',
            'freelancerPayment',
        ]);

        return response()->json($project);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role->value !== 'client' && $user->role->value !== 'admin') {
            abort(403, 'Only clients can create projects.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:project_categories,id'],
        ]);

        $project = Project::create([
            'client_id' => $user->role->value === 'client' ? $user->id : $request->client_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        if ($request->has('categories')) {
            $project->categories()->sync($request->categories);
        }

        event(new ProjectCreated($project));

        $project->load(['client:id,name', 'categories:id,name']);

        return response()->json($project, 201);
    }

    public function assign(Request $request, Project $project)
    {
        if ($request->user()->role->value !== 'admin') {
            abort(403);
        }

        $request->validate([
            'freelancer_id' => ['required', 'exists:users,id'],
        ]);

        $project->update([
            'freelancer_id' => $request->freelancer_id,
            'status' => 'assigned',
        ]);

        $project->load(['client:id,name', 'freelancer:id,name']);

        return response()->json($project);
    }

    public function updateStatus(Request $request, Project $project)
    {
        $user = $request->user();

        $request->validate([
            'status' => ['required', 'string'],
        ]);

        if ($user->role->value === 'freelancer' && $project->freelancer_id !== $user->id) {
            abort(403);
        }

        $project->update(['status' => $request->status]);

        return response()->json($project);
    }

    public function uploadFile(Request $request, Project $project)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $path = $request->file('file')->store('project-files/' . $project->id, 'public');

        $file = $project->files()->create(['file_path' => $path]);

        return response()->json($file, 201);
    }

    public function categories()
    {
        $categories = ProjectCategory::whereNull('parent_id')
            ->with('children:id,name,slug,parent_id')
            ->get(['id', 'name', 'slug', 'description', 'image_path']);

        return response()->json($categories);
    }
}
