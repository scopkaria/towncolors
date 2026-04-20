<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $items = Portfolio::with('freelancer')
            ->latest()
            ->get()
            ->groupBy('status');

        return view('admin.portfolio.index', compact('items'));
    }

    public function approve(Portfolio $portfolio): RedirectResponse
    {
        $portfolio->update(['status' => 'approved']);

        return back()->with('success', 'Portfolio item approved and is now publicly visible.');
    }

    public function reject(Portfolio $portfolio): RedirectResponse
    {
        $portfolio->update(['status' => 'rejected']);

        return back()->with('success', 'Portfolio item rejected.');
    }

    public function edit(Portfolio $portfolio): View
    {
        return view('admin.portfolio.edit', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'project_url' => ['nullable', 'url', 'max:255'],
            'industry' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'completion_year' => ['nullable', 'integer', 'between:2000,2100'],
            'duration' => ['nullable', 'string', 'max:120'],
            'services' => ['nullable', 'string', 'max:500'],
            'technologies' => ['nullable', 'string', 'max:500'],
            'results' => ['nullable', 'string', 'max:2000'],
            'featured' => ['nullable', 'boolean'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'item_type' => ['required', 'in:project,product'],
            'is_purchasable' => ['nullable', 'boolean'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['nullable', 'string', 'max:10'],
            'purchase_url' => ['nullable', 'url', 'max:255'],
        ]);

        $portfolio->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'client_name' => $validated['client_name'] ?? null,
            'project_url' => $validated['project_url'] ?? null,
            'industry' => $validated['industry'] ?? null,
            'country' => $validated['country'] ?? null,
            'completion_year' => $validated['completion_year'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'services' => $this->parseTags($validated['services'] ?? null),
            'technologies' => $this->parseTags($validated['technologies'] ?? null),
            'results' => $validated['results'] ?? null,
            'featured' => (bool) ($validated['featured'] ?? false),
            'status' => $validated['status'],
            'item_type' => $validated['item_type'],
            'is_purchasable' => (bool) ($validated['is_purchasable'] ?? false),
            'price' => $validated['price'] ?? null,
            'currency' => $validated['currency'] ?? 'USD',
            'purchase_url' => $validated['purchase_url'] ?? null,
        ]);

        $redirectRoute = $portfolio->item_type === 'product' ? 'admin.shop.index' : 'admin.portfolio.index';

        return redirect()->route($redirectRoute)->with('success', 'Item updated successfully.');
    }

    public function destroy(Portfolio $portfolio): RedirectResponse
    {
        $isProduct = $portfolio->item_type === 'product';
        $portfolio->delete();

        $redirectRoute = $isProduct ? 'admin.shop.index' : 'admin.portfolio.index';

        return redirect()->route($redirectRoute)->with('success', 'Item deleted successfully.');
    }

    private function parseTags(?string $input): array
    {
        if (! $input) {
            return [];
        }

        return collect(explode(',', $input))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
