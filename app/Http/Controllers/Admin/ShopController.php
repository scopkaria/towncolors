<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Portfolio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        $products = Portfolio::where('item_type', 'product')
            ->latest()
            ->get();

        $imageMedia = Media::query()
            ->where('file_type', 'image')
            ->latest()
            ->limit(300)
            ->get(['id', 'file_name', 'file_path']);

        return view('admin.shop.index', compact('products', 'imageMedia'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'product_description' => ['nullable', 'string'],
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
            'is_purchasable' => ['nullable', 'boolean'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'currency' => ['nullable', 'string', 'max:10'],
            'purchase_url' => ['nullable', 'url', 'max:255'],
            'extra_info' => ['nullable', 'string', 'max:4000'],
            'image_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'gallery_media_ids' => ['nullable', 'array'],
            'gallery_media_ids.*' => ['integer', 'exists:media,id'],
            'gallery_uploads' => ['nullable', 'array'],
            'gallery_uploads.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $imagePath = null;
        if (! empty($validated['image_media_id'])) {
            $media = Media::find($validated['image_media_id']);
            $imagePath = $media?->file_path;
        } elseif ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('portfolio/' . $request->user()->id, 'public');
        }

        $gallery = collect();
        if (! empty($validated['gallery_media_ids'])) {
            $gallery = Media::query()
                ->whereIn('id', $validated['gallery_media_ids'])
                ->where('file_type', 'image')
                ->pluck('file_path');
        }

        if ($request->hasFile('gallery_uploads')) {
            foreach ($request->file('gallery_uploads') as $file) {
                $gallery->push($file->store('portfolio/' . $request->user()->id . '/gallery', 'public'));
            }
        }

        $gallery = $gallery->filter()->unique()->values()->all();

        Portfolio::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $this->makeUniqueSlug($validated['title']),
            'description' => $validated['description'] ?? null,
            'product_description' => $validated['product_description'] ?? null,
            'client_name' => $validated['client_name'] ?? null,
            'project_url' => $validated['project_url'] ?? null,
            'industry' => $validated['industry'] ?? null,
            'country' => $validated['country'] ?? null,
            'completion_year' => $validated['completion_year'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'services' => $this->parseTags($validated['services'] ?? null),
            'technologies' => $this->parseTags($validated['technologies'] ?? null),
            'results' => $validated['results'] ?? null,
            'extra_info' => $validated['extra_info'] ?? null,
            'featured' => (bool) ($validated['featured'] ?? false),
            'image_path' => $imagePath,
            'product_gallery' => $gallery,
            'status' => $validated['status'],
            'item_type' => 'product',
            'is_purchasable' => (bool) ($validated['is_purchasable'] ?? false),
            'price' => $validated['price'] ?? null,
            'currency' => $validated['currency'] ?? 'USD',
            'purchase_url' => $validated['purchase_url'] ?? null,
        ]);

        return back()->with('success', 'Shop product added successfully.');
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
