<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Subscriptions</span>
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Subscription Requests</h1>
                <p class="text-sm text-brand-muted">Review and approve client subscription requests.</p>
                <p class="text-xs text-brand-muted">Live updates are enabled. This table refreshes automatically when requests change.</p>
            </div>
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

    {{-- Approve Modal --}}
    <div x-data="{ open: false, reqId: '', userName: '', planName: '', cycle: '' }"
         @open-approve.window="open = true; reqId = $event.detail.id; userName = $event.detail.userName; planName = $event.detail.planName; cycle = $event.detail.cycle"
         @keydown.escape.window="open = false">
        <div x-cloak x-show="open"
             class="fixed inset-0 z-50 flex items-center justify-center bg-navy-900/70 backdrop-blur-sm px-4 py-8">
            <div @click.outside="open = false"
                 class="w-full max-w-md rounded-3xl border border-white/70 bg-warm-100 shadow-panel">
                <div class="flex items-center justify-between px-6 py-5 border-b border-warm-300/40">
                    <div>
                        <h3 class="font-display text-lg text-brand-ink">Approve Request</h3>
                        <p class="text-xs text-brand-muted mt-0.5" x-text="userName + ' — ' + planName + ' (' + cycle + ')'"></p>
                    </div>
                    <button @click="open = false" class="rounded-xl border border-warm-300/50 p-1.5 text-brand-muted hover:text-brand-ink">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.5"><path d="M6 6l8 8M14 6l-8 8" stroke-linecap="round"/></svg>
                    </button>
                </div>
                <form method="POST" :action="'{{ url('admin/subscription-requests') }}/' + reqId + '/approve'" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-brand-muted mb-1.5">Start Date</label>
                            <input type="date" name="start_date" value="{{ now()->toDateString() }}" required
                                   class="w-full rounded-xl border border-warm-300/50 px-3 py-2 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-brand-muted mb-1.5">Expiry Date</label>
                            <input type="date" name="expiry_date" value="{{ now()->addYear()->toDateString() }}" required
                                   class="w-full rounded-xl border border-warm-300/50 px-3 py-2 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-brand-muted mb-1.5">Admin Notes (optional)</label>
                        <textarea name="admin_notes" rows="2" maxlength="500"
                                  class="w-full rounded-xl border border-warm-300/50 px-3 py-2 text-sm text-brand-ink focus:border-brand-primary focus:outline-none resize-none"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full rounded-xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        Approve & Activate Subscription
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div x-data="{ open: false, reqId: '', userName: '' }"
         @open-reject.window="open = true; reqId = $event.detail.id; userName = $event.detail.userName"
         @keydown.escape.window="open = false">
        <div x-cloak x-show="open"
             class="fixed inset-0 z-50 flex items-center justify-center bg-navy-900/70 backdrop-blur-sm px-4 py-8">
            <div @click.outside="open = false"
                 class="w-full max-w-sm rounded-3xl border border-white/70 bg-warm-100 shadow-panel">
                <div class="flex items-center justify-between px-6 py-5 border-b border-warm-300/40">
                    <div>
                        <h3 class="font-display text-lg text-brand-ink">Reject Request</h3>
                        <p class="text-xs text-brand-muted mt-0.5" x-text="userName"></p>
                    </div>
                    <button @click="open = false" class="rounded-xl border border-warm-300/50 p-1.5 text-brand-muted hover:text-brand-ink">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.5"><path d="M6 6l8 8M14 6l-8 8" stroke-linecap="round"/></svg>
                    </button>
                </div>
                <form method="POST" :action="'{{ url('admin/subscription-requests') }}/' + reqId + '/reject'" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-brand-muted mb-1.5">Reason (optional)</label>
                        <textarea name="admin_notes" rows="2" maxlength="500"
                                  placeholder="Explain why the request is being rejected..."
                                  class="w-full rounded-xl border border-warm-300/50 px-3 py-2 text-sm text-brand-ink focus:border-brand-primary focus:outline-none resize-none"></textarea>
                    </div>
                    <button type="submit"
                            class="w-full rounded-xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700">
                        Reject Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-warm-300/40">
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Client</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Plan</th>
                    <th class="hidden px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted sm:table-cell">Billing</th>
                    <th class="hidden px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted lg:table-cell">Subscribed</th>
                    <th class="hidden px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted xl:table-cell">Payment</th>
                    <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Status</th>
                    <th class="hidden px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted md:table-cell">Submitted</th>
                    <th class="px-5 py-4 text-right text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-warm-200/50">
                @forelse ($requests as $req)
                    <tr class="hover:bg-warm-200/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-medium text-brand-ink">{{ $req->user?->name }}</p>
                            <p class="text-xs text-brand-muted">{{ $req->user?->email }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-brand-ink">{{ $req->plan?->name ?? '—' }}</p>
                        </td>
                        <td class="hidden px-5 py-4 capitalize text-brand-muted sm:table-cell">{{ $req->billing_cycle }}</td>
                        <td class="hidden px-5 py-4 lg:table-cell">
                            @php
                                $hasActive = (bool) $req->user?->subscriptions?->first();
                            @endphp
                            @if ($hasActive)
                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Subscribed</span>
                            @else
                                <span class="inline-flex rounded-full bg-warm-200 px-2.5 py-0.5 text-xs font-semibold text-warm-700">Not Subscribed</span>
                            @endif
                        </td>
                        <td class="hidden px-5 py-4 xl:table-cell">
                            <p class="text-xs font-semibold text-brand-ink">{{ strtoupper($req->payment_method ?? 'N/A') }}</p>
                            @if ($req->payment_reference)
                                <p class="text-[11px] text-brand-muted">Ref: {{ $req->payment_reference }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $req->statusBadge() }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="hidden px-5 py-4 text-brand-muted md:table-cell">{{ $req->created_at->diffForHumans() }}</td>
                        <td class="px-5 py-4 text-right">
                            @if ($req->status === 'pending')
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button"
                                            x-data
                                            @click="$dispatch('open-approve', { id: {{ $req->id }}, userName: '{{ addslashes($req->user?->name) }}', planName: '{{ addslashes($req->plan?->name) }}', cycle: '{{ $req->billing_cycle }}' })"
                                            class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                        Approve
                                    </button>
                                    <button type="button"
                                            x-data
                                            @click="$dispatch('open-reject', { id: {{ $req->id }}, userName: '{{ addslashes($req->user?->name) }}' })"
                                            class="rounded-xl border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                        Reject
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-brand-muted">
                                    {{ $req->reviewed_at?->format('M d, Y') ?? '—' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-16 text-center text-sm text-brand-muted">No subscription requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($requests->hasPages())
            <div class="border-t border-warm-300/40 px-5 py-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    @php
        $adminSnapshot = $snapshot ?? [
            'counts' => ['pending' => 0, 'approved' => 0, 'rejected' => 0],
            'latest' => null,
        ];
    @endphp

    <script>
        (function () {
            var endpoint = @json(route('admin.subscription-requests.snapshot'));
            var initialState = JSON.stringify(@json($adminSnapshot));
            var isChecking = false;

            async function checkForChanges() {
                if (document.hidden || isChecking) {
                    return;
                }

                isChecking = true;

                try {
                    var response = await fetch(endpoint, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store'
                    });

                    if (!response.ok) {
                        return;
                    }

                    var payload = await response.json();
                    var currentState = JSON.stringify({
                        counts: payload.counts,
                        latest: payload.latest,
                    });

                    if (currentState !== initialState) {
                        window.location.reload();
                    }
                } catch (error) {
                    // Keep polling even if one request fails.
                } finally {
                    isChecking = false;
                }
            }

            setInterval(checkForChanges, 8000);
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    checkForChanges();
                }
            });
        })();
    </script>
</x-app-layout>
