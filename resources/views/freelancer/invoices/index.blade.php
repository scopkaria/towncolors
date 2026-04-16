<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                My invoices
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Invoices</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Invoices for projects you're working on.</p>
            </div>
        </div>
    </x-slot>

    {{-- Summary Stats --}}
    @php
        $totalCount = $invoices->count();
        $paidCount  = $invoices->where('status', 'paid')->count();
        $unpaidCount = $invoices->where('status', 'unpaid')->count();
        $tzs = $invoices->sum(fn ($inv) => $inv->amountIn('TZS'));
        $usd = $invoices->sum(fn ($inv) => $inv->amountIn('USD'));
    @endphp

    <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-muted">Total</p>
            <p class="mt-1 font-display text-xl text-brand-ink">{{ $totalCount }}</p>
        </div>
        <div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-muted">Paid</p>
            <p class="mt-1 font-display text-xl text-emerald-600">{{ $paidCount }}</p>
        </div>
        <div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-muted">Unpaid</p>
            <p class="mt-1 font-display text-xl text-amber-600">{{ $unpaidCount }}</p>
        </div>
        <div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card">
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-muted">Revenue (TZS)</p>
            <p class="mt-1 font-display text-lg text-brand-primary">TZS {{ number_format($tzs, 2) }}</p>
            <p class="text-[10px] text-brand-muted">≈ ${{ number_format($usd, 2) }}</p>
        </div>
    </div>

    {{-- Invoice List --}}
    @if ($invoices->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-warm-200">
                <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            </div>
            <h3 class="mt-4 font-display text-lg text-brand-ink">No invoices yet</h3>
            <p class="mt-2 text-sm text-brand-muted">Invoices for your projects will appear here.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($invoices as $invoice)
                <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-card transition hover:shadow-panel">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex rounded-lg border border-warm-300/40 bg-warm-200/50 px-2 py-0.5 text-[10px] font-bold text-brand-muted">
                                    INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                @if ($invoice->status === 'paid')
                                    <span class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Paid</span>
                                @else
                                    <span class="inline-flex rounded-lg border border-amber-200 bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700">Unpaid</span>
                                @endif
                                <span class="inline-flex rounded-lg border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-600">{{ $invoice->currency }}</span>
                            </div>
                            <p class="truncate font-display text-sm text-brand-ink">{{ $invoice->project->title }}</p>
                            <p class="text-xs text-brand-muted">
                                Client: <span class="font-medium">{{ $invoice->project->client->name ?? 'N/A' }}</span>
                                &middot; {{ $invoice->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-display text-lg text-brand-ink">{{ $invoice->formattedAmount() }}</p>
                            @if ($invoice->converted_amount)
                                <p class="text-xs text-brand-muted">
                                    ≈ {{ $invoice->currency === 'USD' ? 'TZS ' . number_format($invoice->converted_amount, 2) : '$' . number_format($invoice->converted_amount, 2) }}
                                </p>
                            @endif
                            @if ($invoice->exchange_rate)
                                <p class="mt-1 text-[10px] text-brand-muted/60">Rate: 1 USD = {{ number_format($invoice->exchange_rate, 2) }} TZS</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 border-t border-warm-300/40 pt-3">
                        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn-secondary inline-flex w-full justify-center text-center text-xs">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                            Download Invoice (PDF)
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
