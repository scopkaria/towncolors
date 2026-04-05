<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Events\ProjectAssigned;
use App\Events\ProjectStatusChanged;
use App\Models\Project;
use App\Models\User;
use App\Services\AiAssistant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::with(['client', 'freelancer', 'categories'])->withCount(['files'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->boolean('unassigned')) {
            $query->whereNull('freelancer_id');
        }

        $projects      = $query->get();
        $currentStatus = $request->input('status');
        $unassigned    = $request->boolean('unassigned');
        $freelancers   = User::where('role', 'freelancer')->orderBy('name')->get();

        $counts = [
            'all'         => Project::count(),
            'pending'     => Project::where('status', 'pending')->count(),
            'assigned'    => Project::where('status', 'assigned')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'completed'   => Project::where('status', 'completed')->count(),
            'unassigned'  => Project::whereNull('freelancer_id')->count(),
        ];

        return view('admin.projects.index', compact('projects', 'currentStatus', 'unassigned', 'freelancers', 'counts'));
    }

    public function show(Project $project): View
    {
        $project->load(['client', 'freelancer', 'files', 'categories', 'freelancerPayment.logs']);
        $freelancers   = User::where('role', 'freelancer')->orderBy('name')->get();
        $statuses      = ['pending', 'assigned', 'in_progress', 'completed'];
        $aiSuggestions = app(AiAssistant::class)->suggestFreelancers($project);

        return view('admin.projects.show', compact('project', 'freelancers', 'statuses', 'aiSuggestions'));
    }

    public function assign(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'freelancer_id' => ['required', 'exists:users,id'],
        ]);

        $freelancer = User::where('id', $validated['freelancer_id'])
            ->where('role', 'freelancer')
            ->firstOrFail();

        $project->update([
            'freelancer_id' => $freelancer->id,
            'status'        => 'assigned',
        ]);

        ProjectAssigned::dispatch($project, $freelancer);

        return back()->with('success', 'Freelancer assigned successfully.');
    }

    public function updateStatus(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,assigned,in_progress,completed'],
        ]);

        $oldStatus = $project->status;
        $project->update(['status' => $validated['status']]);

        ProjectStatusChanged::dispatch($project->load(['client', 'freelancer']), $validated['status'], $oldStatus, $request->user()->id);

        return back()->with('success', 'Status updated successfully.');
    }
}
