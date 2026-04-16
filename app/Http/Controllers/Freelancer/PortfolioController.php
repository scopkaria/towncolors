<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $items = Portfolio::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('freelancer.portfolio.index', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
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
            'item_type' => ['nullable', 'in:project,product'],
            'is_purchasable' => ['nullable', 'boolean'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['nullable', 'string', 'max:10'],
            'purchase_url' => ['nullable', 'url', 'max:255'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store(
                'portfolio/' . $request->user()->id,
                'public'
            );
        }

        Portfolio::create([
            'user_id'     => $request->user()->id,
            'title'       => $validated['title'],
            'slug'        => $this->makeUniqueSlug($validated['title']),
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
            'item_type' => $validated['item_type'] ?? 'project',
            'is_purchasable' => (bool) ($validated['is_purchasable'] ?? false),
            'price' => $validated['price'] ?? null,
            'currency' => $validated['currency'] ?? 'USD',
            'purchase_url' => $validated['purchase_url'] ?? null,
            'image_path'  => $imagePath,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Portfolio item submitted and pending approval.');
    }

    public function destroy(Request $request, Portfolio $portfolio): RedirectResponse
    {
        abort_unless($portfolio->user_id === $request->user()->id, 403);

        if ($portfolio->image_path) {
            Storage::disk('public')->delete($portfolio->image_path);
        }

        $portfolio->delete();

        return back()->with('success', 'Portfolio item deleted.');
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

    private function makeUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $index = 2;

        while (Portfolio::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $index;
            $index++;
        }

        return $slug;
    }
}
