<?php

namespace App\Http\Controllers\Client;

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
            ->where('client_id', $request->user()->id)
            ->with(['assignee'])
            ->latest()
            ->paginate(15);

        return view('client.tasks.index', compact('tasks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'voice_note' => ['nullable', 'file', 'max:10240', 'mimetypes:audio/mpeg,audio/wav,audio/x-wav,audio/ogg,audio/mp4,audio/webm'],
            'task_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        $task = ClientTask::create([
            'client_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'],
            'status' => 'pending',
        ]);

        if ($request->hasFile('voice_note')) {
            $task->update([
                'voice_note_path' => $request->file('voice_note')->store('client-tasks/voice-notes', 'public'),
            ]);
        }

        if ($request->hasFile('task_image')) {
            $task->update([
                'image_path' => $request->file('task_image')->store('client-tasks/images', 'public'),
            ]);
        }

        User::where('role', UserRole::ADMIN)
            ->get()
            ->each(fn (User $admin) => $admin->notify(new TaskEventNotification(
                title: 'New client task submitted',
                message: $request->user()->name . ' submitted a new task: "' . $task->title . '". Review and assign it.',
                subject: 'New Client Task Submitted',
                actionUrl: route('admin.client-tasks.index'),
                actionText: 'Open Task Inbox',
                taskId: $task->id,
                eventType: 'submitted',
                note: $task->priority === 'urgent' ? 'Priority: Urgent' : 'Priority: ' . ucfirst($task->priority),
            )));

        return back()->with('success', 'Task submitted successfully. Admin will review and assign it.');
    }
}
