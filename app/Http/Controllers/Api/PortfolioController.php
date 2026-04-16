<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $query = Portfolio::approved()->with('freelancer:id,name');

        if ($request->has('freelancer_id')) {
            $query->where('user_id', $request->freelancer_id);
        }

        $portfolios = $query->latest()->paginate(20);

        return response()->json($portfolios);
    }

    public function myPortfolio(Request $request)
    {
        $user = $request->user();

        if ($user->role->value !== 'freelancer') {
            abort(403);
        }

        $items = Portfolio::where('user_id', $user->id)->latest()->paginate(20);

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role->value !== 'freelancer') {
            abort(403);
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
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
            'image' => ['nullable', 'image', 'max:10240'],
        ]);

        $path = $request->file('image')
            ? $request->file('image')->store('portfolios', 'public')
            : null;

        $portfolio = Portfolio::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'slug' => $this->makeUniqueSlug($request->title),
            'description' => $request->description,
            'client_name' => $request->client_name,
            'project_url' => $request->project_url,
            'industry' => $request->industry,
            'country' => $request->country,
            'completion_year' => $request->completion_year,
            'duration' => $request->duration,
            'services' => $this->parseTags($request->services),
            'technologies' => $this->parseTags($request->technologies),
            'results' => $request->results,
            'featured' => (bool) $request->boolean('featured'),
            'item_type' => $request->input('item_type', 'project'),
            'is_purchasable' => (bool) $request->boolean('is_purchasable'),
            'price' => $request->input('price'),
            'currency' => $request->input('currency', 'USD'),
            'purchase_url' => $request->input('purchase_url'),
            'image_path' => $path,
            'status' => 'pending',
        ]);

        return response()->json($portfolio, 201);
    }

    public function destroy(Request $request, Portfolio $portfolio)
    {
        $user = $request->user();

        if ($user->role->value !== 'freelancer' || $portfolio->user_id !== $user->id) {
            abort(403);
        }

        $portfolio->delete();

        return response()->json(['message' => 'Deleted']);
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
