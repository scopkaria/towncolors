<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\View\View;

class ExperienceController extends Controller
{
    public function index(): View
    {
        $experiences = ProjectCategory::whereNull('parent_id')
            ->withCount('projects')
            ->orderBy('name')
            ->get();

        return view('experiences.index', compact('experiences'));
    }
}
