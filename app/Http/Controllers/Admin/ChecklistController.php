<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ClientChecklistItem;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChecklistController extends Controller
{
    public function show(User $user): View
    {
        abort_unless($user->role === UserRole::CLIENT, 404);

        $items = $user->checklistItems()->get();

        return view('admin.checklists.show', compact('user', 'items'));
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === UserRole::CLIENT, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        ClientChecklistItem::create([
            'client_id' => $user->id,
            'created_by' => $request->user()->id,
            'title' => $data['title'],
            'status' => $data['status'],
            'sort_order' => ((int) $user->checklistItems()->max('sort_order')) + 1,
        ]);

        return back()->with('success', 'Checklist item added.');
    }

    public function update(Request $request, User $user, ClientChecklistItem $item): RedirectResponse
    {
        abort_unless($user->id === $item->client_id && $user->role === UserRole::CLIENT, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        $item->update($data);

        return back()->with('success', 'Checklist item updated.');
    }

    public function destroy(User $user, ClientChecklistItem $item): RedirectResponse
    {
        abort_unless($user->id === $item->client_id && $user->role === UserRole::CLIENT, 404);

        $item->delete();

        return back()->with('success', 'Checklist item removed.');
    }

    public function snapshot(User $user): JsonResponse
    {
        abort_unless($user->role === UserRole::CLIENT, 404);

        $latest = $user->checklistItems()->latest('updated_at')->latest('id')->first();

        return response()->json([
            'count' => $user->checklistItems()->count(),
            'latest' => $latest ? [
                'id' => $latest->id,
                'status' => $latest->status,
                'updated_at' => optional($latest->updated_at)->toIso8601String(),
            ] : null,
        ]);
    }
}