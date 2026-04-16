<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Project;
use App\Notifications\UserAlertNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        return app(ConversationController::class)->index($request);
    }

    public function show(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeAccess($request, $project);

        $conversation = Conversation::findOrCreateForProject($project);
        $role         = $request->user()->role->value;

        return redirect(route($role . '.messages') . '?conversation=' . $conversation->id);
    }

    public function fetchMessages(Request $request, Project $project): JsonResponse
    {
        $this->authorizeAccess($request, $project);

        $conversation = Conversation::findOrCreateForProject($project);

        return app(ConversationController::class)->fetchMessages($request, $conversation);
    }

    public function store(Request $request, Project $project): JsonResponse
    {
        $this->authorizeAccess($request, $project);

        $conversation = Conversation::findOrCreateForProject($project);

        return app(ConversationController::class)->sendMessage($request, $conversation);
    }

    private function authorizeAccess(Request $request, Project $project): void
    {
        $user = $request->user();

        if ($user->role === UserRole::CLIENT && ! $user->hasActiveSubscription()) {
            abort(403, 'An active subscription is required to access project chat.');
        }

        if ($user->role === UserRole::ADMIN) {
            return;
        }

        if ($user->role === UserRole::CLIENT && $project->client_id === $user->id) {
            return;
        }

        if ($user->role === UserRole::FREELANCER && $project->freelancer_id === $user->id) {
            return;
        }

        abort(403, 'You do not have access to this chat.');
    }
}
