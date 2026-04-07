<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
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
            'message' => ['required', 'string', 'max:5000'],
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

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'message_type' => 'text',
        ]);

        $conversation->touch();

        $conversation->load(['users:id,name', 'latestMessage.sender:id,name']);

        return response()->json($conversation, 201);
    }
}
