<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                My earnings
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Earnings</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Track agreed amounts and payments received across all your projects.</p>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Summary Stats --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-muted">Total Agreed</p>
            <p class="mt-3 font-display text-2xl text-brand-ink">TZS {{ number_format($totalAgreed, 2) }}</p>
        </div>
        <div class="card-premium rounded-3xl border border-emerald-100 bg-emerald-50 p-6 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-600">Total Received</p>
            <p class="mt-3 font-display text-2xl text-emerald-700">TZS {{ number_format($totalPaid, 2) }}</p>
        </div>
        <div class="card-premium rounded-3xl border border-accent/20 bg-accent-light p-6 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-brand-primary">Outstanding</p>
            <p class="mt-3 font-display text-2xl text-brand-primary">TZS {{ number_format($totalRemaining, 2) }}</p>
        </div>
    </div>

    {{-- Per-Project Breakdown --}}
    @if ($payments->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-warm-200">
                <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </div>
            <h3 class="mt-4 font-display text-xl text-brand-ink">No payment records yet</h3>
            <p class="mt-2 text-sm text-brand-muted">An admin will set your agreed payment once a project is assigned to you.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($payments as $payment)
                @php
                    $statusClass = match($payment->status) {
                        'paid'    => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                        'partial' => 'border-amber-200 bg-amber-50 text-amber-700',
                        default   => 'border-warm-300/50 bg-warm-200/50 text-warm-700',
                    };
                @endphp
                <div x-data="{ open: false }" class="rounded-3xl border border-white/70 bg-white/90 shadow-panel">
                    <button type="button"
                            @click="open = !open"
                            class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left">
                        <div class="min-w-0">
                            <p class="font-display text-lg text-brand-ink truncate">{{ $payment->project->title }}</p>
                            <p class="mt-1 text-xs text-brand-muted">{{ $payment->project->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span class="inline-flex rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] {{ $statusClass }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                            <svg class="h-5 w-5 text-brand-muted transition duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </div>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition duration-200 ease-out"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition duration-150 ease-in"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="border-t border-warm-300/40 px-6 pb-6 pt-5">
                        {{-- Amounts --}}
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-warm-300/40 bg-warm-200/50 px-4 py-4 text-center">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">Agreed</p>
                                <p class="mt-2 font-display text-xl text-brand-ink">{{ $payment->formattedAgreed() }}</p>
                            </div>
                            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4 text-center">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Received</p>
                                <p class="mt-2 font-display text-xl text-emerald-700">{{ $payment->formattedPaid() }}</p>
                            </div>
                            <div class="rounded-2xl border border-accent/20 bg-accent-light px-4 py-4 text-center">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-primary">Remaining</p>
                                <p class="mt-2 font-display text-xl text-brand-primary">{{ $payment->formattedRemaining() }}</p>
                            </div>
                        </div>

                        {{-- Progress bar --}}
                        @if ((float) $payment->agreed_amount > 0)
                            @php $pct = min(100, round(($payment->paid_amount / $payment->agreed_amount) * 100)); @endphp
                            <div class="mt-5">
                                <div class="mb-1 flex items-center justify-between text-xs text-brand-muted">
                                    <span>Payment progress</span>
                                    <span>{{ $pct }}%</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-warm-200">
                                    <div class="h-full rounded-full bg-brand-primary transition-all duration-500"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endif

                        {{-- Payment History --}}
                        @if ($payment->logs->isNotEmpty())
                            <div class="mt-5">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">Payment History</p>
                                <div class="mt-3 space-y-2">
                                    @foreach ($payment->logs->sortByDesc('created_at') as $log)
                                        <div class="flex items-center justify-between rounded-2xl border border-warm-300/40 bg-warm-200/60 px-4 py-3 text-sm">
                                            <span class="font-semibold text-brand-ink">TZS {{ number_format($log->amount, 2) }}</span>
                                            <span class="text-xs text-brand-muted">{{ $log->created_at->format('M d, Y  H:i') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="mt-4 text-sm text-brand-muted">No payments received yet.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
