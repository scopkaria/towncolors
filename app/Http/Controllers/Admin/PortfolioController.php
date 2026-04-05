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
}
