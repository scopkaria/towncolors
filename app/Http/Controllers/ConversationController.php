<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use App\Notifications\UserAlertNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    /**
     * Render the main two-panel chat hub.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations  = $this->getConversationsForUser($user);
        $availableUsers = $this->getAvailableDMUsers($user);

        return view('chat.index', compact('conversations', 'availableUsers'));
    }

    /**
     * Return conversations list as JSON (used by the front-end poller).
     */
    public function list(Request $request): JsonResponse
    {
        return response()->json($this->getConversationsForUser($request->user()));
    }

    /**
     * Create a new direct (DM) conversation.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id', 'not_in:' . $user->id],
        ]);

        $target = User::findOrFail($request->integer('user_id'));

        $this->authorizeDirectMessage($user, $target);

        // Return existing conversation if it already exists
        $existing = Conversation::where('type', 'direct')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $target->id))
            ->first();

        if ($existing) {
            return response()->json(['id' => $existing->id]);
        }

        $conversation = Conversation::create(['type' => 'direct']);
        $conversation->participants()->createMany([
            ['user_id' => $user->id],
            ['user_id' => $target->id],
        ]);

        return response()->json(['id' => $conversation->id], 201);
    }

    /**
     * Fetch messages for a conversation (JSON).
     */
    public function fetchMessages(Request $request, Conversation $conversation): JsonResponse
    {
        $this->authorizeConversationAccess($request->user(), $conversation);

        $after = $request->integer('after', 0);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->when($after, fn ($q) => $q->where('id', '>', $after))
            ->get()
            ->map(fn (Message $msg) => $this->formatMessage($msg));

        // Mark as read
        $conversation->participants()
            ->where('user_id', $request->user()->id)
            ->update(['last_read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Send a message to a conversation (JSON).
     */
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        $this->authorizeConversationAccess($user, $conversation);

        $request->validate([
            'message'      => ['nullable', 'string', 'max:5000'],
            'message_type' => ['nullable', 'string', 'in:text,image,document,audio,location'],
            'file'         => ['nullable', 'file', 'max:20480'],
            'latitude'     => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'    => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $type = $request->input('message_type', 'text');

        if ($type === 'location') {
            if (! $request->filled('latitude') || ! $request->filled('longitude')) {
                return response()->json(['error' => 'Location coordinates are required.'], 422);
            }
        } elseif (in_array($type, ['image', 'document', 'audio'], true)) {
            if (! $request->hasFile('file')) {
                return response()->json(['error' => 'A file is required for this message type.'], 422);
            }
        } else {
            if (! $request->filled('message') && ! $request->hasFile('file')) {
                return response()->json(['error' => 'Please provide a message or file.'], 422);
            }
            if ($request->hasFile('file') && ! $request->filled('message')) {
                $mime = $request->file('file')->getMimeType();
                if (str_starts_with($mime, 'image/')) {
                    $type = 'image';
                } elseif (str_starts_with($mime, 'audio/')) {
                    $type = 'audio';
                } else {
                    $type = 'document';
                }
            }
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $folder   = $conversation->project_id
                ? 'messages/' . $conversation->project_id
                : 'messages/dm/' . $conversation->id;
            $filePath = $request->file('file')->store($folder, 'public');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'project_id'      => $conversation->project_id,
            'sender_id'       => $user->id,
            'message'         => $request->input('message'),
            'message_type'    => $type,
            'file_path'       => $filePath,
            'latitude'        => $request->input('latitude'),
            'longitude'       => $request->input('longitude'),
        ]);

        $conversation->touch();
        $message->load('sender');

        // Notify all other participants
        $conversation->participants()
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->reject(fn ($u) => $u->id === $user->id)
            ->each(function ($recipient) use ($message, $user, $conversation) {
                $actionUrl = route($recipient->role->value . '.messages')
                    . '?conversation=' . $conversation->id;

                $recipient->notify(new UserAlertNotification(
                    'New message',
                    $user->name . ' sent a message.',
                    'New message',
                    $actionUrl,
                    'View message',
                    projectId: $conversation->project_id,
                ));
            });

        return response()->json($this->formatMessage($message), 201);
    }

    // -----------------------------------------------------------------
    //  Helpers
    // -----------------------------------------------------------------

    private function getConversationsForUser(User $user): array
    {
        $query = Conversation::with(['participants.user', 'latestMessage.sender', 'project'])
            ->latest('updated_at');

        if ($user->role !== UserRole::ADMIN) {
            $query->whereHas('participants', fn ($q) => $q->where('user_id', $user->id));
        }

        return $query->get()
            ->map(fn (Conversation $c) => $this->formatConversation($c, $user))
            ->all();
    }

    private function formatConversation(Conversation $c, User $user): array
    {
        $others = $c->participants
            ->map(fn ($p) => $p->user)
            ->filter()
            ->reject(fn ($u) => $u->id === $user->id)
            ->values();

        if ($c->type === 'project') {
            $title    = $c->project?->title ?? 'Project Chat';
            $subtitle = $others->pluck('name')->join(' · ');
        } else {
            $other    = $others->first();
            $title    = $other?->name ?? 'Direct Message';
            $subtitle = $other?->role?->label() ?? '';
        }

        $avatarText  = strtoupper(substr($title, 0, 2));
        $avatarColor = $c->type === 'project' ? 'orange' : 'blue';
        $lastMsg     = $c->latestMessage;
        $roomName    = 'tc-' . ($c->project_id ? 'project-' . $c->project_id : 'chat-' . $c->id);

        return [
            'id'           => $c->id,
            'type'         => $c->type,
            'project_id'   => $c->project_id,
            'room_name'    => $roomName,
            'title'        => $title,
            'subtitle'     => $subtitle,
            'avatar_text'  => $avatarText,
            'avatar_color' => $avatarColor,
            'last_message' => $lastMsg ? [
                'text'   => $lastMsg->message ?? '📎 ' . ($lastMsg->message_type ?? 'Attachment'),
                'sender' => $lastMsg->sender?->name ?? '',
                'mine'   => $lastMsg->sender_id === $user->id,
                'time'   => $lastMsg->created_at->diffForHumans(short: true),
            ] : null,
            'fetch_url'    => route('conversations.messages', $c),
            'send_url'     => route('conversations.send', $c),
        ];
    }

    private function getAvailableDMUsers(User $user): array
    {
        $query = User::where('id', '!=', $user->id)->orderBy('name');

        if ($user->role === UserRole::FREELANCER) {
            $query->whereIn('role', [UserRole::FREELANCER->value, UserRole::ADMIN->value]);
        } elseif ($user->role === UserRole::CLIENT) {
            $query->where('role', UserRole::ADMIN->value);
        }
        // Admin: no extra filter (can message everyone)

        return $query->get(['id', 'name', 'role'])
            ->map(fn ($u) => [
                'id'       => $u->id,
                'name'     => $u->name,
                'role'     => $u->role->label(),
                'initials' => collect(explode(' ', $u->name))
                    ->filter()
                    ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
                    ->take(2)
                    ->implode(''),
            ])
            ->all();
    }

    private function formatMessage(Message $msg): array
    {
        $fileUrl = $msg->file_path ? asset('storage/' . $msg->file_path) : null;

        return [
            'id'           => $msg->id,
            'sender_id'    => $msg->sender_id,
            'sender_name'  => $msg->sender->name,
            'message'      => $msg->message,
            'message_type' => $msg->message_type ?? 'text',
            'file_path'    => $fileUrl,
            'file_name'    => $msg->file_path ? basename($msg->file_path) : null,
            'latitude'     => $msg->latitude ? (float) $msg->latitude : null,
            'longitude'    => $msg->longitude ? (float) $msg->longitude : null,
            'created_at'   => $msg->created_at->format('M d, Y h:i A'),
        ];
    }

    private function authorizeConversationAccess(User $user, Conversation $conversation): void
    {
        if ($user->role === UserRole::ADMIN) {
            return;
        }

        abort_unless(
            $conversation->participants()->where('user_id', $user->id)->exists(),
            403,
            'You do not have access to this conversation.'
        );
    }

    private function authorizeDirectMessage(User $sender, User $target): void
    {
        if ($sender->role === UserRole::ADMIN) {
            return;
        }

        if ($sender->role === UserRole::FREELANCER) {
            abort_unless(
                $target->role === UserRole::FREELANCER || $target->role === UserRole::ADMIN,
                403,
                'Freelancers can only message other freelancers or admins.'
            );

            return;
        }

        if ($sender->role === UserRole::CLIENT) {
            abort_unless(
                $target->role === UserRole::ADMIN,
                403,
                'Clients can only send direct messages to admins.'
            );
        }
    }
}
