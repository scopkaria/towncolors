<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $categories = ProjectCategory::whereNull('parent_id')
            ->orderBy('name')
            ->take(6)
            ->get();

        $portfolios = Portfolio::approved()
            ->with('freelancer')
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('categories', 'portfolios'));
    }
}
