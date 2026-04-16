<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = FaqItem::orderBy('category')->orderBy('sort_order')->orderBy('id')->get();
        $categoryOptions = $faqs
            ->flatMap(fn (FaqItem $item) => $item->categories_list)
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('admin.faq.index', compact('faqs', 'categoryOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', 'max:100'],
            'new_categories' => ['nullable', 'string', 'max:300'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $categories = collect($validated['categories'] ?? [])
            ->merge(explode(',', (string) ($validated['new_categories'] ?? '')))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->values();

        if ($categories->isEmpty()) {
            $fallback = trim((string) ($validated['category'] ?? 'General'));
            $categories = collect([$fallback !== '' ? $fallback : 'General']);
        }

        $validated['category'] = $categories->first();
        $validated['categories'] = $categories->all();
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);
        unset($validated['new_categories']);

        FaqItem::create($validated);

        return back()->with('success', 'FAQ created successfully.');
    }

    public function update(Request $request, FaqItem $faq): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', 'max:100'],
            'new_categories' => ['nullable', 'string', 'max:300'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $categories = collect($validated['categories'] ?? [])
            ->merge(explode(',', (string) ($validated['new_categories'] ?? '')))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->values();

        if ($categories->isEmpty()) {
            $fallback = trim((string) ($validated['category'] ?? 'General'));
            $categories = collect([$fallback !== '' ? $fallback : 'General']);
        }

        $validated['category'] = $categories->first();
        $validated['categories'] = $categories->all();
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');
        unset($validated['new_categories']);

        $faq->update($validated);

        return back()->with('success', 'FAQ updated successfully.');
    }

    public function destroy(FaqItem $faq): RedirectResponse
    {
        $faq->delete();

        return back()->with('success', 'FAQ deleted successfully.');
    }
}
