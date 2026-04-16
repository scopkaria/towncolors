<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function show(Request $request): View
    {
        $user         = $request->user();
        $subscription = $user->activeSubscription();
        $isSubscribed = (bool) $subscription;
        $isTrialActive = $user->hasActiveTrial();
        $hasFullAccess = $user->hasFullAccess();
        $settings = Setting::instance();
        $enabledPaymentMethods = $settings->enabledPaymentMethods();
        $plans        = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $history = $user->subscriptions()
            ->with('plan')
            ->latest()
            ->take(5)
            ->get();

        $pendingRequest = SubscriptionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('plan')
            ->latest()
            ->first();

        $requestHistory = SubscriptionRequest::where('user_id', $user->id)
            ->with(['plan', 'reviewer'])
            ->latest()
            ->take(10)
            ->get();

        return view('client.subscription.show', compact(
            'user',
            'subscription',
            'isSubscribed',
            'isTrialActive',
            'hasFullAccess',
            'enabledPaymentMethods',
            'settings',
            'plans',
            'history',
            'pendingRequest',
            'requestHistory'
        ));
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        $subscription = $user->subscriptions()
            ->with('plan')
            ->latest('updated_at')
            ->latest('id')
            ->first();

        $pendingRequest = SubscriptionRequest::where('user_id', $user->id)
            ->with('plan')
            ->latest('updated_at')
            ->latest('id')
            ->first();

        return response()->json([
            'is_subscribed' => (bool) $user->activeSubscription(),
            'is_trial_active' => $user->hasActiveTrial(),
            'has_full_access' => $user->hasFullAccess(),
            'active_subscription_id' => $user->activeSubscription()?->id,
            'subscription' => $subscription ? [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'plan_id' => $subscription->plan_id,
                'expiry_date' => optional($subscription->expiry_date)->toDateString(),
                'updated_at' => optional($subscription->updated_at)->toIso8601String(),
            ] : null,
            'request' => $pendingRequest ? [
                'id' => $pendingRequest->id,
                'status' => $pendingRequest->status,
                'plan_id' => $pendingRequest->plan_id,
                'updated_at' => optional($pendingRequest->updated_at)->toIso8601String(),
            ] : null,
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
