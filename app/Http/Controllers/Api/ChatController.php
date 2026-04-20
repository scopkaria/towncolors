<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function conversations(Request $request)
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with(['latestMessage.sender:id,name', 'project:id,title', 'users:id,name'])
            ->withCount(['messages as unread_count' => function ($q) use ($user) {
                $q->where('created_at', '>', function ($sub) use ($user) {
                    $sub->select('last_read_at')
                        ->from('conversation_participants')
                        ->where('conversation_id', \DB::raw('conversations.id'))
                        ->where('user_id', $user->id)
                        ->limit(1);
                })->orWhereNull(
                    \DB::raw("(SELECT last_read_at FROM conversation_participants WHERE conversation_id = conversations.id AND user_id = {$user->id})")
                );
            }])
            ->latest('updated_at')
            ->paginate(20);

        return response()->json($conversations);
    }

    public function messages(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if (! $conversation->users()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        // Update last read
        $conversation->users()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->latest()
            ->paginate(50);

        return response()->json($messages);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if (! $conversation->users()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        $request->validate([
            'message' => ['required_without:file', 'nullable', 'string', 'max:5000'],
            'message_type' => ['nullable', 'in:text,image,document,audio,location'],
            'file' => ['nullable', 'file', 'max:20480'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $data = [
            'conversation_id' => $conversation->id,
            'project_id' => $conversation->project_id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'message_type' => $request->message_type ?? 'text',
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('chat-files/' . $conversation->id, 'public');
            if (! $request->message_type) {
                $ext = $request->file('file')->getClientOriginalExtension();
                $data['message_type'] = in_array($ext, ['jpg','jpeg','png','gif','webp']) ? 'image' : 'document';
            }
        }

        if ($request->latitude && $request->longitude) {
            $data['latitude'] = $request->latitude;
            $data['longitude'] = $request->longitude;
            $data['message_type'] = 'location';
        }

        $message = Message::create($data);

        $conversation->touch();

        $message->load('sender:id,name');

        return response()->json($message, 201);
    }

    public function createConversation(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        // Find existing DM conversation
        $existing = Conversation::where('type', 'direct')
            ->whereHas('users', fn($q) => $q->where('user_id', $user->id))
            ->whereHas('users', fn($q) => $q->where('user_id', $request->user_id))
            ->first();

        if ($existing) {
            $conversation = $existing;
        } else {
            $conversation = Conversation::create(['type' => 'direct']);
            $conversation->users()->attach([$user->id, $request->user_id]);
        }

        if ($request->message) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'message' => $request->message,
                'message_type' => 'text',
            ]);

            $conversation->touch();
        }

        $conversation->load(['users:id,name', 'latestMessage.sender:id,name']);

        return response()->json($conversation, 201);
    }

    public function findOrCreateByProject(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
        ]);

        $project = Project::findOrFail($request->project_id);

        // Find existing project conversation
        $conversation = Conversation::where('project_id', $project->id)->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'type' => 'project',
                'project_id' => $project->id,
            ]);

            // Attach relevant users (owner + freelancer + admins)
            $userIds = collect([$project->user_id, $project->freelancer_id])
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $conversation->users()->attach($userIds);
        }

        // Ensure the requesting user is part of the conversation
        if (! $conversation->users()->where('user_id', $user->id)->exists()) {
            $conversation->users()->attach($user->id);
        }

        $conversation->load(['users:id,name', 'project:id,title', 'latestMessage.sender:id,name']);

        return response()->json($conversation);
    }

    /**
     * Get contacts for the current user based on their role.
     * Admin: sees all clients and freelancers.
     * Client: sees admins + freelancers assigned to their projects.
     * Freelancer: sees admins + clients whose projects they are assigned to.
     */
    public function contacts(Request $request)
    {
        $user = $request->user();
        $role = $user->role->value;

        if ($role === 'admin') {
            $contacts = User::where('id', '!=', $user->id)
                ->select('id', 'name', 'email', 'role')
                ->orderBy('role')
                ->orderBy('name')
                ->get();
        } elseif ($role === 'client') {
            // Admins + freelancers assigned to their projects
            $freelancerIds = Project::where('user_id', $user->id)
                ->whereNotNull('freelancer_id')
                ->pluck('freelancer_id')
                ->unique();

            $contacts = User::where('id', '!=', $user->id)
                ->where(function ($q) use ($freelancerIds) {
                    $q->where('role', 'admin')
                      ->orWhereIn('id', $freelancerIds);
                })
                ->select('id', 'name', 'email', 'role')
                ->orderBy('role')
                ->orderBy('name')
                ->get();
        } else {
            // Freelancer: admins + clients whose projects they're on
            $clientIds = Project::where('freelancer_id', $user->id)
                ->pluck('user_id')
                ->unique();

            $contacts = User::where('id', '!=', $user->id)
                ->where(function ($q) use ($clientIds) {
                    $q->where('role', 'admin')
                      ->orWhereIn('id', $clientIds);
                })
                ->select('id', 'name', 'email', 'role')
                ->orderBy('role')
                ->orderBy('name')
                ->get();
        }

        return response()->json(['data' => $contacts]);
    }
}
