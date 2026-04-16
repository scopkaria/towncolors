<?php

namespace App\Http\Controllers;

use App\Models\FaqItem;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $items = FaqItem::active()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $grouped = collect();

        foreach ($items as $item) {
            foreach ($item->categories_list as $category) {
                $grouped->put(
                    $category,
                    ($grouped->get($category) ?? collect())->push($item)
                );
            }
        }

        $faqs = $grouped
            ->sortKeys()
            ->map(fn ($list) => $list->sortBy(['sort_order', 'id'])->values());

        return view('site.faq', compact('faqs'));
    }
}
