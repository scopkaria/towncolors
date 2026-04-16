<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Subscriptions</span>
            <h1 class="font-display text-3xl text-brand-ink">Assign Subscription — {{ $user->name }}</h1>
            <p class="text-sm text-brand-muted">{{ $user->email }}</p>
        </div>
    </x-slot>

    <div class="max-w-xl">
        @if ($current)
            <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
                <strong>Current plan:</strong> {{ $current->plan?->name ?? '—' }} ·
                Expires {{ $current->expiry_date->format('M d, Y') }} ·
                <span class="capitalize">{{ $current->status }}</span>
                <p class="mt-1 text-xs">Assigning a new plan will cancel the existing active subscription.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.subscriptions.store', $user) }}" class="space-y-6">
            @csrf

            <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-card space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Plan</label>
                    <select name="plan_id" required
                            class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        <option value="">Select a plan…</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} — ${{ number_format($plan->price_monthly, 2) }}/mo
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Billing cycle</label>
                    <select name="billing_cycle"
                            class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        <option value="monthly" {{ old('billing_cycle') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly"  {{ old('billing_cycle') === 'yearly'  ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink mb-1.5">Start date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required
                               class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink mb-1.5">Expiry date</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', now()->addMonth()->toDateString()) }}" required
                               class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Status</label>
                    <select name="status"
                            class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        @foreach (['active', 'pending', 'cancelled', 'expired'] as $s)
                            <option value="{{ $s }}" {{ old('status', 'active') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Notes <span class="font-normal text-brand-muted">(optional)</span></label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none"
                              placeholder="Internal notes about this subscription…">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="rounded-2xl bg-navy-800 px-6 py-2.5 text-sm font-semibold text-white shadow-card transition hover:bg-slate-800">
                    Assign Plan
                </button>
                <a href="{{ route('admin.subscriptions.index') }}"
                   class="rounded-2xl border border-warm-300/50 px-6 py-2.5 text-sm font-semibold text-brand-muted transition hover:text-brand-ink">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
