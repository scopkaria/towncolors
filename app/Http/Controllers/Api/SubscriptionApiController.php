<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientChecklistItem;
use App\Models\ClientFile;
use App\Models\ClientFolder;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubscriptionApiController extends Controller
{
    /** List active plans + user's subscription status */
    public function plans(Request $request): JsonResponse
    {
        $user  = $request->user();
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'slug'          => $p->slug,
                'color'         => $p->color,
                'price_monthly' => $p->price_monthly,
                'price_yearly'  => $p->price_yearly,
                'features'      => $p->features ?? [],
            ]);

        $active   = $user->activeSubscription();
        $settings = Setting::instance();

        return response()->json([
            'plans'   => $plans,
            'status'  => [
                'is_subscribed'    => (bool) $active,
                'is_trial_active'  => $user->hasActiveTrial(),
                'has_full_access'  => $user->hasFullAccess(),
                'can_start_trial'  => $user->canStartTrial(),
                'trial_end_date'   => $user->trial_end_date?->toDateString(),
            ],
            'active_subscription' => $active ? [
                'id'           => $active->id,
                'plan'         => $active->plan?->name,
                'plan_color'   => $active->plan?->color,
                'billing_cycle'=> $active->billing_cycle,
                'start_date'   => $active->start_date?->toDateString(),
                'expiry_date'  => $active->expiry_date?->toDateString(),
                'days_left'    => $active->daysUntilExpiry(),
                'status'       => $active->status,
            ] : null,
            'payment_methods' => ! empty($settings->enabledPaymentMethods())
                ? $settings->enabledPaymentMethods()
                : ['manual_review' => 'Manual Review'],
            'mpesa_paybill'   => $settings->mpesa_paybill ?? null,
        ]);
    }

    /** Submit a subscription request */
    public function requestSubscription(Request $request): JsonResponse
    {
        $enabledMethods = array_keys(Setting::instance()->enabledPaymentMethods());

        $allowedMethods = empty($enabledMethods) ? ['manual_review'] : $enabledMethods;
        if (empty($enabledMethods) && ! $request->filled('payment_method')) {
            $request->merge(['payment_method' => 'manual_review']);
        }

        $data = $request->validate([
            'plan_id'           => ['required', 'exists:subscription_plans,id'],
            'billing_cycle'     => ['required', 'in:monthly,yearly'],
            'payment_method'    => ['required', 'in:' . implode(',', $allowedMethods)],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();

        $pending = SubscriptionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return response()->json(['message' => 'You already have a pending subscription request.'], 422);
        }

        $sub = SubscriptionRequest::create(array_merge($data, ['user_id' => $user->id]));

        return response()->json([
            'message' => 'Subscription request submitted successfully.',
            'request' => [
                'id'     => $sub->id,
                'status' => 'pending',
            ],
        ], 201);
    }

    /** Start free trial */
    public function startTrial(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role->value !== 'client') {
            return response()->json(['message' => 'Only clients can start a trial.'], 403);
        }

        if (! $user->canStartTrial()) {
            return response()->json(['message' => 'Free trial already used for this account.'], 422);
        }

        $user->startFreeTrial(5);

        return response()->json([
            'message'        => 'Your free trial is now active for 5 days!',
            'trial_end_date' => $user->fresh()->trial_end_date?->toDateString(),
        ]);
    }

    /** Request history */
    public function requestHistory(Request $request): JsonResponse
    {
        $requests = SubscriptionRequest::where('user_id', $request->user()->id)
            ->with('plan')
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($r) => [
                'id'              => $r->id,
                'plan'            => $r->plan?->name,
                'billing_cycle'   => $r->billing_cycle,
                'payment_method'  => $r->payment_method,
                'status'          => $r->status,
                'admin_notes'     => $r->admin_notes,
                'created_at'      => $r->created_at->toIso8601String(),
                'reviewed_at'     => $r->reviewed_at?->toIso8601String(),
            ]);

        return response()->json(['requests' => $requests]);
    }
}
