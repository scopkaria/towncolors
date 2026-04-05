<?php

namespace App\Http\Controllers\Freelancer;

use App\Events\ProjectFileUploaded;
use App\Events\ProjectStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::where('freelancer_id', $request->user()->id)
            ->with(['client', 'categories'])
            ->latest()
            ->get();

        return view('freelancer.projects.index', compact('projects'));
    }

    public function show(Request $request, Project $project): View
    {
        abort_unless($project->freelancer_id === $request->user()->id, 403);

        $project->load(['client', 'files']);

        return view('freelancer.projects.show', compact('project'));
    }

    public function updateStatus(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->freelancer_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:in_progress,completed'],
        ]);

        $oldStatus = $project->status;
        $project->update(['status' => $validated['status']]);

        ProjectStatusChanged::dispatch(
            $project->load(['client', 'freelancer']),
            $validated['status'],
            $oldStatus,
            $request->user()->id,
        );

        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * Accept file uploads from the freelancer and notify the client.
     * Automatically upgrades the project status from "assigned" to "in_progress".
     */
    public function uploadFile(Request $request, Project $project): RedirectResponse
    {
        abort_unless($project->freelancer_id === $request->user()->id, 403);
        abort_unless(in_array($project->status, ['assigned', 'in_progress']), 403);

        $request->validate([
            'files'   => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['file', 'max:20480'],
        ]);

        $uploadCount = 0;

        foreach ($request->file('files') as $file) {
            $path = $file->store('projects/' . $project->id . '/deliverables', 'public');
            ProjectFile::create([
                'project_id' => $project->id,
                'file_path'  => $path,
            ]);
            $uploadCount++;
        }

        // Auto-progress: assigned → in_progress on first file upload
        $oldStatus = $project->status;
        if ($oldStatus === 'assigned') {
            $project->update(['status' => 'in_progress']);

            ProjectStatusChanged::dispatch(
                $project->load(['client', 'freelancer']),
                'in_progress',
                $oldStatus,
                $request->user()->id,
            );
        }

        // Notify the client about uploaded deliverables
        ProjectFileUploaded::dispatch($project, $uploadCount, $request->user());

        return back()->with('success', "{$uploadCount} file(s) uploaded successfully.");
    }
}
