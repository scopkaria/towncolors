<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;

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
            'image' => ['required', 'image', 'max:10240'],
        ]);

        $path = $request->file('image')->store('portfolios', 'public');

        $portfolio = Portfolio::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
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
}
