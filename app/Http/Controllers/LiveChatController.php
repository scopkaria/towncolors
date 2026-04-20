<?php

namespace App\Http\Controllers;

use App\Models\LiveChatMessage;
use App\Models\LiveChatSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LiveChatController extends Controller
{
    // ─── Public (visitor) endpoints ──────────────────────────────────

    /**
     * Start or resume a chat session. Stores session_key in the visitor's browser.
     */
    public function startSession(Request $request): JsonResponse
    {
        $request->validate([
            'session_key' => 'nullable|string|max:64',
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|max:150',
        ]);

        $sessionKey = $request->input('session_key');

        // Try to resume an existing open session
        if ($sessionKey) {
            $session = LiveChatSession::where('session_key', $sessionKey)
                ->whereIn('status', ['waiting', 'active'])
                ->first();

            if ($session) {
                return response()->json([
                    'session_key' => $session->session_key,
                    'status'      => $session->status,
                ]);
            }
        }

        // Create a new session
        $session = LiveChatSession::create([
            'session_key'   => Str::random(48),
            'visitor_name'  => $request->input('name'),
            'visitor_email' => $request->input('email'),
            'status'        => 'waiting',
        ]);

        return response()->json([
            'session_key' => $session->session_key,
            'status'      => $session->status,
        ], 201);
    }

    /**
     * Visitor sends a message.
     */
    public function visitorSend(Request $request): JsonResponse
    {
        $request->validate([
            'session_key' => 'required|string|max:64',
            'body'        => 'required|string|max:2000',
        ]);

        $session = LiveChatSession::where('session_key', $request->input('session_key'))
            ->whereIn('status', ['waiting', 'active'])
            ->firstOrFail();

        $message = $session->messages()->create([
            'sender_type' => 'visitor',
            'body'        => $request->input('body'),
        ]);

        return response()->json($message, 201);
    }

    /**
     * Visitor polls for new messages.
     */
    public function visitorMessages(Request $request): JsonResponse
    {
        $request->validate([
            'session_key' => 'required|string|max:64',
            'after'       => 'nullable|integer|min:0',
        ]);

        $session = LiveChatSession::where('session_key', $request->input('session_key'))
            ->firstOrFail();

        $query = $session->messages()->orderBy('id');

        if ($request->filled('after')) {
            $query->where('id', '>', $request->input('after'));
        }

        return response()->json([
            'status'   => $session->status,
            'messages' => $query->get(),
        ]);
    }

    // ─── Admin / Support-Agent endpoints ─────────────────────────────

    /**
     * API: List live chat sessions as JSON (for mobile app).
     */
    public function apiSessions(Request $request): JsonResponse
    {
        $query = LiveChatSession::with('agent:id,name')
            ->withCount('messages')
            ->orderByRaw("FIELD(status, 'waiting', 'active', 'closed')")
            ->latest();

        // Optional status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->paginate(25));
    }

    /**
     * List all live chat sessions (admin dashboard view).
     */
    public function adminIndex(Request $request)
    {
        $sessions = LiveChatSession::with('agent:id,name')
            ->withCount('messages')
            ->orderByRaw("FIELD(status, 'waiting', 'active', 'closed')")
            ->latest()
            ->paginate(25);

        return view('admin.live-chat.index', compact('sessions'));
    }

    /**
     * Show a specific chat session for the agent to respond.
     */
    public function adminShow(LiveChatSession $session)
    {
        $messages = $session->messages()
            ->with('agent:id,name')
            ->orderBy('id')
            ->get();

        return view('admin.live-chat.show', compact('session', 'messages'));
    }

    /**
     * Agent claims / joins a session.
     */
    public function agentJoin(Request $request, LiveChatSession $session): JsonResponse
    {
        if ($session->status === 'closed') {
            return response()->json(['error' => 'Session is closed.'], 422);
        }

        $session->update([
            'agent_id' => $request->user()->id,
            'status'   => 'active',
        ]);

        return response()->json(['status' => 'active']);
    }

    /**
     * Agent sends a message.
     */
    public function agentSend(Request $request, LiveChatSession $session): JsonResponse
    {
        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        if ($session->status === 'closed') {
            return response()->json(['error' => 'Session is closed.'], 422);
        }

        // Auto-claim if not yet assigned
        if (!$session->agent_id) {
            $session->update([
                'agent_id' => $request->user()->id,
                'status'   => 'active',
            ]);
        }

        $message = $session->messages()->create([
            'sender_type' => 'agent',
            'agent_id'    => $request->user()->id,
            'body'        => $request->input('body'),
        ]);

        return response()->json($message, 201);
    }

    /**
     * Agent polls for new messages in a session.
     */
    public function agentMessages(Request $request, LiveChatSession $session): JsonResponse
    {
        $after = $request->integer('after', 0);

        $query = $session->messages()->with('agent:id,name')->orderBy('id');
        if ($after > 0) {
            $query->where('id', '>', $after);
        }

        return response()->json([
            'status'   => $session->status,
            'messages' => $query->get(),
        ]);
    }

    /**
     * Close a session.
     */
    public function agentClose(LiveChatSession $session): JsonResponse
    {
        $session->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        return response()->json(['status' => 'closed']);
    }

    /**
     * Session history — return closed sessions with message counts.
     */
    public function sessionHistory(Request $request): JsonResponse
    {
        $sessions = LiveChatSession::with('agent:id,name')
            ->withCount('messages')
            ->where('status', 'closed')
            ->latest('closed_at')
            ->paginate(25);

        return response()->json($sessions);
    }
}
