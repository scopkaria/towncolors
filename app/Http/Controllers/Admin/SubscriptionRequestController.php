<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionRequestController extends Controller
{
    public function index(): View
    {
        $requests = SubscriptionRequest::with([
                'user.subscriptions' => function ($query) {
                    $query->where('status', 'active')
                        ->where('expiry_date', '>=', now()->toDateString())
                        ->latest();
                },
                'plan',
                'reviewer',
            ])
            ->latest()
            ->paginate(20);

        $latest = SubscriptionRequest::query()->latest('updated_at')->latest('id')->first();

        $snapshot = [
            'counts' => [
                'pending' => SubscriptionRequest::where('status', 'pending')->count(),
                'approved' => SubscriptionRequest::where('status', 'approved')->count(),
                'rejected' => SubscriptionRequest::where('status', 'rejected')->count(),
            ],
            'latest' => $latest ? [
                'id' => $latest->id,
                'status' => $latest->status,
                'updated_at' => optional($latest->updated_at)->toIso8601String(),
            ] : null,
        ];

        return view('admin.subscription-requests.index', compact('requests', 'snapshot'));
    }

    public function approve(Request $request, SubscriptionRequest $subscriptionRequest): RedirectResponse
    {
        if ($subscriptionRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $data = $request->validate([
            'start_date'  => ['required', 'date'],
            'expiry_date' => ['required', 'date', 'after:start_date'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Cancel any existing active subscription
        $subscriptionRequest->user->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Create active subscription
        Subscription::create([
            'user_id'       => $subscriptionRequest->user_id,
            'plan_id'       => $subscriptionRequest->plan_id,
            'billing_cycle' => $subscriptionRequest->billing_cycle,
            'start_date'    => $data['start_date'],
            'expiry_date'   => $data['expiry_date'],
            'status'        => 'active',
            'notes'         => $data['admin_notes'] ?? null,
        ]);

        $subscriptionRequest->update([
            'status'      => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        return back()->with('success', "Subscription approved for {$subscriptionRequest->user->name}.");
    }

    public function reject(Request $request, SubscriptionRequest $subscriptionRequest): RedirectResponse
    {
        if ($subscriptionRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been reviewed.');
        }

        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $subscriptionRequest->update([
            'status'      => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        return back()->with('success', "Request from {$subscriptionRequest->user->name} rejected.");
    }

    public function snapshot(): JsonResponse
    {
        $latest = SubscriptionRequest::query()->latest('updated_at')->latest('id')->first();

        return response()->json([
            'counts' => [
                'pending' => SubscriptionRequest::where('status', 'pending')->count(),
                'approved' => SubscriptionRequest::where('status', 'approved')->count(),
                'rejected' => SubscriptionRequest::where('status', 'rejected')->count(),
            ],
            'latest' => $latest ? [
                'id' => $latest->id,
                'status' => $latest->status,
                'updated_at' => optional($latest->updated_at)->toIso8601String(),
            ] : null,
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
