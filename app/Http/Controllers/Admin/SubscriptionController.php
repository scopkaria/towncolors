<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('plan'),   fn ($q) => $q->where('plan_id', $request->plan))
            ->latest()
            ->paginate(20);

        $plans = SubscriptionPlan::orderBy('sort_order')->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'plans'));
    }

    public function assign(User $user): View
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        $current = $user->subscriptions()->with('plan')->latest()->first();

        return view('admin.subscriptions.assign', compact('user', 'plans', 'current'));
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'plan_id'       => ['required', 'exists:subscription_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'start_date'    => ['required', 'date'],
            'expiry_date'   => ['required', 'date', 'after:start_date'],
            'status'        => ['required', 'in:active,expired,cancelled,pending'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $data['user_id'] = $user->id;

        // Cancel any previously active subscription
        $user->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        Subscription::create($data);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', "Subscription assigned to {$user->name}.");
    }

    public function edit(Subscription $subscription): View
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.subscriptions.edit', compact('subscription', 'plans'));
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $data = $request->validate([
            'plan_id'       => ['required', 'exists:subscription_plans,id'],
            'billing_cycle' => ['required', 'in:monthly,yearly'],
            'start_date'    => ['required', 'date'],
            'expiry_date'   => ['required', 'date', 'after:start_date'],
            'status'        => ['required', 'in:active,expired,cancelled,pending'],
            'notes'         => ['nullable', 'string', 'max:500'],
        ]);

        $subscription->update($data);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription updated.');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription removed.');
    }

    public function revoke(Request $request, Subscription $subscription): RedirectResponse
    {
        if (in_array($subscription->status, ['cancelled', 'expired'], true)) {
            return back()->with('error', 'This subscription is already inactive.');
        }

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $existingNotes = trim((string) $subscription->notes);
        $reason = trim((string) ($data['reason'] ?? ''));
        $revocationNote = 'Revoked by admin on ' . now()->format('M d, Y H:i');

        if ($reason !== '') {
            $revocationNote .= ': ' . $reason;
        }

        $subscription->update([
            'status' => 'cancelled',
            'expiry_date' => now()->toDateString(),
            'notes' => $existingNotes !== '' ? ($existingNotes . "\n" . $revocationNote) : $revocationNote,
        ]);

        $subscriberName = $subscription->user?->name ?? 'deleted user';

        return back()->with('success', "Subscription for {$subscriberName} has been revoked.");
    }
}
