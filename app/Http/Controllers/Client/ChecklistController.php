<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChecklistController extends Controller
{
    public function show(Request $request): View
    {
        $items = $request->user()->checklistItems()->get();

        return view('client.checklist.show', compact('items'));
    }

    public function snapshot(Request $request): JsonResponse
    {
        $latest = $request->user()->checklistItems()->latest('updated_at')->latest('id')->first();

        return response()->json([
            'count' => $request->user()->checklistItems()->count(),
            'latest' => $latest ? [
                'id' => $latest->id,
                'status' => $latest->status,
                'updated_at' => optional($latest->updated_at)->toIso8601String(),
            ] : null,
        ]);
    }
}