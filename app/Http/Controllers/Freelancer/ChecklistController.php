<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChecklistController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $assignedClientIds = Project::where('freelancer_id', $user->id)
            ->whereNotNull('client_id')
            ->distinct()
            ->pluck('client_id');

        $clients = \App\Models\User::whereIn('id', $assignedClientIds)
            ->with(['checklistItems' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')])
            ->orderBy('name')
            ->get();

        return view('freelancer.checklist.show', compact('clients'));
    }
}
