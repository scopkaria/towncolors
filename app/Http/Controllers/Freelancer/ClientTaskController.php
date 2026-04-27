<?php

namespace App\Http\Controllers\Freelancer;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ClientTask;
use App\Models\User;
use App\Notifications\TaskEventNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientTaskController extends Controller
{
    public function index(Request $request): View
    {
        $tasks = ClientTask::query()
            ->where('assigned_type', 'freelancer')
            ->where('assigned_to', $request->user()->id)
            ->with(['client', 'assigner'])
            ->latest()
            ->paginate(20);

        return view('freelancer.client-tasks.index', compact('tasks'));
    }

    public function updateStatus(Request $request, ClientTask $clientTask): RedirectResponse
    {
        abort_unless(
            $clientTask->assigned_type === 'freelancer' && $clientTask->assigned_to === $request->user()->id,
            403
        );

        $data = $request->validate([
            'status' => ['required', 'in:assigned,in_progress,completed'],
        ]);

        $oldStatus = $clientTask->status;

        $clientTask->update([
            'status' => $data['status'],
        ]);

        if ($oldStatus !== $clientTask->status) {
            $statusLabel = ucwords(str_replace('_', ' ', $clientTask->status));

            $clientTask->client?->notify(new TaskEventNotification(
                title: 'Task status updated',
                message: 'Your task "' . $clientTask->title . '" is now ' . $statusLabel . '.',
                subject: 'Task Status Updated',
                actionUrl: route('client.tasks.index'),
                actionText: 'View Tasks',
                taskId: $clientTask->id,
                eventType: 'status_changed',
            ));

            User::where('role', UserRole::ADMIN)
                ->get()
                ->each(fn (User $admin) => $admin->notify(new TaskEventNotification(
                    title: 'Client task status changed',
                    message: $request->user()->name . ' changed task "' . $clientTask->title . '" to ' . $statusLabel . '.',
                    subject: 'Client Task Status Changed',
                    actionUrl: route('admin.client-tasks.index'),
                    actionText: 'Open Task Inbox',
                    taskId: $clientTask->id,
                    eventType: 'status_changed',
                )));
        }

        return back()->with('success', 'Task status updated.');
    }
}
