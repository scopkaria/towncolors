<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Setting;
use App\Models\SoftwarePurchaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        $products = Portfolio::approved()
            ->where('item_type', 'product')
            ->where('is_purchasable', true)
            ->with('freelancer:id,name')
            ->orderByDesc('featured')
            ->latest()
            ->get();

        $settings = Setting::instance();
        $enabledMethods = $settings->enabledPaymentMethods();

        return view('shop.index', compact('products', 'settings', 'enabledMethods'));
    }

    public function showProduct(Portfolio $portfolio): View
    {
        abort_unless(
            $portfolio->status === 'approved'
                && $portfolio->item_type === 'product'
                && $portfolio->is_purchasable,
            404
        );

        $relatedProducts = Portfolio::approved()
            ->where('item_type', 'product')
            ->where('is_purchasable', true)
            ->whereKeyNot($portfolio->id)
            ->orderByDesc('featured')
            ->latest()
            ->take(8)
            ->get();

        return view('shop.show', [
            'product' => $portfolio,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function checkout(Portfolio $portfolio): View
    {
        abort_unless(
            $portfolio->status === 'approved'
                && $portfolio->item_type === 'product'
                && $portfolio->is_purchasable,
            404
        );

        $settings = Setting::instance();
        $enabledMethods = $settings->enabledPaymentMethods();

        return view('shop.checkout', [
            'product' => $portfolio,
            'settings' => $settings,
            'enabledMethods' => $enabledMethods,
        ]);
    }

    public function storeCheckout(Request $request, Portfolio $portfolio): RedirectResponse
    {
        abort_unless(
            $portfolio->status === 'approved'
                && $portfolio->item_type === 'product'
                && $portfolio->is_purchasable,
            404
        );

        $settings = Setting::instance();
        $enabledMethods = $settings->enabledPaymentMethods();
        $allowedMethods = array_keys($enabledMethods);

        if (empty($allowedMethods)) {
            $allowedMethods = ['manual_request'];
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:' . implode(',', $allowedMethods)],
            'payment_reference' => ['nullable', 'string', 'max:180'],
            'message' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:120'],
        ]);

        $user = $request->user();

        SoftwarePurchaseRequest::create([
            'portfolio_id' => $portfolio->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $validated['phone'] ?? $user->phone,
            'company' => $validated['company'] ?? null,
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('shop.checkout', $portfolio)
            ->with('success', 'Your software request has been sent. Our team will contact you shortly.');
    }
}
