<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Business</span>
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Subscriptions</h1>
                <p class="text-sm text-brand-muted">View and manage all client subscriptions.</p>
            </div>
            <a href="{{ route('admin.subscription-plans.index') }}"
               class="inline-flex items-center gap-2 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm font-semibold text-brand-muted transition hover:text-brand-ink">
                Manage Plans
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 rounded-3xl border border-white/70 bg-white/90 px-5 py-4 shadow-card">
        <select name="status"
                class="rounded-xl border border-warm-300/50 px-3 py-2 text-sm text-brand-ink focus:outline-none">
            <option value="">All statuses</option>
            @foreach (['active', 'expired', 'cancelled', 'pending'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select name="plan"
                class="rounded-xl border border-warm-300/50 px-3 py-2 text-sm text-brand-ink focus:outline-none">
            <option value="">All plans</option>
            @foreach ($plans as $plan)
                <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="rounded-xl border border-warm-300/50 bg-warm-100 px-4 py-2 text-sm font-semibold text-brand-ink transition hover:border-brand-primary/30">
            Filter
        </button>
        @if (request()->hasAny(['status', 'plan']))
            <a href="{{ route('admin.subscriptions.index') }}"
               class="rounded-xl border border-warm-300/50 px-4 py-2 text-sm text-brand-muted transition hover:text-brand-ink">Clear</a>
        @endif
    </form>

    <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-warm-300/40">
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Client</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Plan</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Cycle</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Subscribed</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Expires</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Status</th>
                    <th class="px-5 py-4 text-right text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-200/50">
                @forelse ($subscriptions as $sub)
                    <tr class="hover:bg-warm-200/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-brand-ink">{{ $sub->user->name }}</p>
                            <p class="text-xs text-brand-muted">{{ $sub->user->email }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if ($sub->plan)
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sub->plan->badgeClass() }}">
                                    {{ $sub->plan->name }}
                                </span>
                            @else
                                <span class="text-brand-muted">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 capitalize text-brand-muted">{{ $sub->billing_cycle }}</td>
                        <td class="px-5 py-4">
                            @if ($sub->isActive())
                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Subscribed</span>
                            @else
                                <span class="inline-flex rounded-full bg-warm-200 px-2.5 py-0.5 text-xs font-semibold text-warm-700">Not Subscribed</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-brand-ink">
                            {{ $sub->expiry_date->format('M d, Y') }}
                            @if ($sub->isExpiringSoon())
                                <span class="ml-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">Soon</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sub->statusBadge() }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.subscriptions.assign', $sub->user) }}"
                                   class="rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs font-semibold text-brand-ink transition hover:border-brand-primary/40">
                                    Assign New
                                </a>
                                <a href="{{ route('admin.subscriptions.edit', $sub) }}"
                                   class="rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs font-semibold text-brand-ink transition hover:border-brand-primary/40">
                                    Edit
                                </a>
                                @if (in_array($sub->status, ['active', 'pending'], true))
                                    <form method="POST" action="{{ route('admin.subscriptions.revoke', $sub) }}"
                                          onsubmit="return confirm('Revoke this subscription now?')">
                                        @csrf
                                        <button type="submit"
                                                class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">
                                            Revoke
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.subscriptions.destroy', $sub) }}"
                                      onsubmit="return confirm('Remove this subscription?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="rounded-xl border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center text-sm text-brand-muted">No subscriptions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($subscriptions->hasPages())
            <div class="border-t border-warm-300/40 px-5 py-4">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
