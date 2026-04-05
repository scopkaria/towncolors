<?php

namespace App\Http\Controllers;

use App\Models\ProjectCategory;
use Illuminate\View\View;

class ServicesController extends Controller
{
    public function index(): View
    {
        $categories = ProjectCategory::whereNull('parent_id')
            ->withCount('projects')
            ->orderBy('name')
            ->get();

        return view('services.index', compact('categories'));
    }

    public function show(ProjectCategory $category): View
    {
        $category->loadCount('projects')->load('children');

        $projects = $category->projects()
            ->with(['client:id,name', 'categories:id,name,color'])
            ->where('status', 'completed')
            ->latest()
            ->get();

        // Also gather sibling/related categories (same parent or other root categories)
        $relatedCategories = ProjectCategory::where('id', '!=', $category->id)
            ->whereNull('parent_id')
            ->withCount('projects')
            ->orderBy('name')
            ->limit(6)
            ->get();

        return view('services.show', compact('category', 'projects', 'relatedCategories'));
    }
}
