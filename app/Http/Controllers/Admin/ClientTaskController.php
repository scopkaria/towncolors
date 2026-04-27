<?php

namespace App\Http\Controllers\Admin;

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
        $status = $request->query('status');
        $assigneeType = $request->query('assignee_type');

        $tasks = ClientTask::query()
            ->with(['client', 'assignee', 'assigner'])
            ->when(in_array($status, ['pending', 'assigned', 'in_progress', 'completed'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when(in_array($assigneeType, ['admin', 'freelancer', 'unassigned'], true), function ($query) use ($assigneeType) {
                if ($assigneeType === 'unassigned') {
                    $query->whereNull('assigned_to');
                    return;
                }

                $query->where('assigned_type', $assigneeType);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $freelancers = User::query()
            ->where('role', UserRole::FREELANCER->value)
            ->orderBy('name')
            ->get(['id', 'name']);

        $admins = User::query()
            ->where('role', UserRole::ADMIN->value)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.client-tasks.index', compact('tasks', 'freelancers', 'admins', 'status', 'assigneeType'));
    }

    public function assign(Request $request, ClientTask $clientTask): RedirectResponse
    {
        $data = $request->validate([
            'assigned_type' => ['required', 'in:admin,freelancer'],
            'assigned_to' => ['required', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $assignee = User::query()->findOrFail((int) $data['assigned_to']);

        if ($data['assigned_type'] === 'admin' && $assignee->role !== UserRole::ADMIN) {
            return back()->with('error', 'Selected assignee is not an admin user.');
        }

        if ($data['assigned_type'] === 'freelancer' && $assignee->role !== UserRole::FREELANCER) {
            return back()->with('error', 'Selected assignee is not a freelancer user.');
        }

        $clientTask->update([
            'assigned_type' => $data['assigned_type'],
            'assigned_to' => $assignee->id,
            'assigned_by' => $request->user()->id,
            'assigned_at' => now(),
            'due_date' => $data['due_date'] ?? null,
            'admin_notes' => $data['admin_notes'] ?? null,
            'status' => $clientTask->status === 'completed' ? 'completed' : 'assigned',
        ]);

        $assignee->notify(new TaskEventNotification(
            title: 'New task assignment',
            message: 'You have been assigned client task "' . $clientTask->title . '".',
            subject: 'Task Assignment',
            actionUrl: $data['assigned_type'] === 'freelancer'
                ? route('freelancer.client-tasks.index')
                : route('admin.client-tasks.index'),
            actionText: 'Open Tasks',
            taskId: $clientTask->id,
            eventType: 'assigned',
            note: ! empty($data['admin_notes']) ? $data['admin_notes'] : null,
        ));

        $clientTask->client?->notify(new TaskEventNotification(
            title: 'Task assigned',
            message: 'Your task "' . $clientTask->title . '" has been assigned to ' . $assignee->name . '.',
            subject: 'Your Task Has Been Assigned',
            actionUrl: route('client.tasks.index'),
            actionText: 'View Tasks',
            taskId: $clientTask->id,
            eventType: 'assigned',
        ));

        return back()->with('success', 'Task assigned successfully.');
    }

    public function updateStatus(Request $request, ClientTask $clientTask): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,assigned,in_progress,completed'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldStatus = $clientTask->status;

        $clientTask->update([
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? $clientTask->admin_notes,
        ]);

        $statusLabel = ucwords(str_replace('_', ' ', $clientTask->status));

        if ($oldStatus !== $clientTask->status) {
            $clientTask->client?->notify(new TaskEventNotification(
                title: 'Task status updated',
                message: 'Your task "' . $clientTask->title . '" is now ' . $statusLabel . '.',
                subject: 'Task Status Updated',
                actionUrl: route('client.tasks.index'),
                actionText: 'View Tasks',
                taskId: $clientTask->id,
                eventType: 'status_changed',
                note: $data['admin_notes'] ?? null,
            ));

            if ($clientTask->assignee) {
                $clientTask->assignee->notify(new TaskEventNotification(
                    title: 'Assigned task status changed',
                    message: 'Task "' . $clientTask->title . '" is now ' . $statusLabel . '.',
                    subject: 'Assigned Task Status Changed',
                    actionUrl: $clientTask->assigned_type === 'freelancer'
                        ? route('freelancer.client-tasks.index')
                        : route('admin.client-tasks.index'),
                    actionText: 'Open Tasks',
                    taskId: $clientTask->id,
                    eventType: 'status_changed',
                    note: $data['admin_notes'] ?? null,
                ));
            }

            User::where('role', UserRole::ADMIN)
                ->where('id', '!=', $request->user()->id)
                ->get()
                ->each(fn (User $admin) => $admin->notify(new TaskEventNotification(
                    title: 'Client task status changed',
                    message: 'Task "' . $clientTask->title . '" is now ' . $statusLabel . '.',
                    subject: 'Client Task Status Changed',
                    actionUrl: route('admin.client-tasks.index'),
                    actionText: 'Open Task Inbox',
                    taskId: $clientTask->id,
                    eventType: 'status_changed',
                    note: $data['admin_notes'] ?? null,
                )));
        }

        return back()->with('success', 'Task status updated.');
    }
}
