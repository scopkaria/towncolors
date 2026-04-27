<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SubscriptionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $enabledMethods = array_keys(Setting::instance()->enabledPaymentMethods());

        // Fallback keeps requests visible to admin even when payments are not configured yet.
        $allowedMethods = empty($enabledMethods) ? ['manual_review'] : $enabledMethods;
        if (empty($enabledMethods) && ! $request->filled('payment_method')) {
            $request->merge(['payment_method' => 'manual_review']);
        }

        $data = $request->validate([
            'plan_id'       => ['required', 'exists:subscription_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'payment_method' => ['required', 'in:' . implode(',', $allowedMethods)],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();

        // Prevent duplicate pending requests
        $pending = SubscriptionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return back()->with('error', 'You already have a pending subscription request. Please wait for admin review.');
        }

        SubscriptionRequest::create(array_merge($data, ['user_id' => $user->id]));

        return back()->with('success', 'Subscription request submitted! We will review it shortly.');
    }
}
