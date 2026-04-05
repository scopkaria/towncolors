<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\View\View;

class PublicPortfolioController extends Controller
{
    public function index(): View
    {
        $items = Portfolio::approved()
            ->with('freelancer:id,name')
            ->latest()
            ->get();

        // Build unique freelancer list for the filter
        $freelancers = $items
            ->pluck('freelancer')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        return view('portfolio.index', compact('items', 'freelancers'));
    }
}
