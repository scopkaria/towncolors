<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\View\View;

class PublicPortfolioController extends Controller
{
    public function index(): View
    {
        $items = Portfolio::query()
            ->whereIn('status', ['approved', 'published'])
            ->with('freelancer:id,name')
            ->orderByRaw("CASE WHEN item_type = 'product' THEN 0 ELSE 1 END")
            ->orderByDesc('featured')
            ->latest()
            ->get();

        if ($items->isEmpty()) {
            $items = Portfolio::query()
                ->with('freelancer:id,name')
                ->orderByRaw("CASE WHEN item_type = 'product' THEN 0 ELSE 1 END")
                ->orderByDesc('featured')
                ->latest()
                ->get();
        }

        // Build unique freelancer list for the filter
        $freelancers = $items
            ->pluck('freelancer')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $categories = collect();
        foreach ($items as $item) {
            if (!empty($item->industry)) {
                $categories->push(trim((string) $item->industry));
            }
            if (is_array($item->services)) {
                foreach ($item->services as $service) {
                    if (!empty($service)) {
                        $categories->push(trim((string) $service));
                    }
                }
            }
        }
        $categories = $categories
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('portfolio.index', compact('items', 'freelancers', 'categories'));
    }
}
