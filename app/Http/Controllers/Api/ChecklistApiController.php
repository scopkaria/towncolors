<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientChecklistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChecklistApiController extends Controller
{
    /** List checklist items for the authenticated client/freelancer */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = ClientChecklistItem::where('client_id', $user->id)
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get()
            ->map(fn ($item) => [
                'id'         => $item->id,
                'title'      => $item->title,
                'status'     => $item->status,
                'sort_order' => $item->sort_order,
                'created_at' => $item->created_at->toIso8601String(),
            ]);

        $counts = [
            'total'       => $items->count(),
            'pending'     => $items->where('status', 'pending')->count(),
            'in_progress' => $items->where('status', 'in_progress')->count(),
            'completed'   => $items->where('status', 'completed')->count(),
        ];

        return response()->json([
            'items'  => $items->values(),
            'counts' => $counts,
        ]);
    }
}
