<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Subscriptions</span>
            <h1 class="font-display text-3xl text-brand-ink">Edit Subscription</h1>
            @if ($subscription->user)
                <p class="text-sm text-brand-muted">{{ $subscription->user->name }} · {{ $subscription->user->email }}</p>
            @else
                <p class="text-sm text-brand-muted">Deleted user · orphaned subscription</p>
            @endif
        </div>
    </x-slot>

    <div class="max-w-xl">
        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}" class="space-y-6">
            @csrf @method('PATCH')

            <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-card space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Plan</label>
                    <select name="plan_id" required
                            class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $subscription->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Billing cycle</label>
                    <select name="billing_cycle"
                            class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        <option value="monthly" {{ old('billing_cycle', $subscription->billing_cycle) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly"  {{ old('billing_cycle', $subscription->billing_cycle) === 'yearly'  ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink mb-1.5">Start date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $subscription->start_date->toDateString()) }}" required
                               class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink mb-1.5">Expiry date</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', $subscription->expiry_date->toDateString()) }}" required
                               class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Status</label>
                    <select name="status"
                            class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        @foreach (['active', 'pending', 'cancelled', 'expired'] as $s)
                            <option value="{{ $s }}" {{ old('status', $subscription->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">{{ old('notes', $subscription->notes) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="rounded-2xl bg-navy-800 px-6 py-2.5 text-sm font-semibold text-white shadow-card transition hover:bg-slate-800">
                    Save Changes
                </button>
                <a href="{{ route('admin.subscriptions.index') }}"
                   class="rounded-2xl border border-warm-300/50 px-6 py-2.5 text-sm font-semibold text-brand-muted transition hover:text-brand-ink">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
