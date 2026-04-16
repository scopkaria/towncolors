<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                My invoices
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Invoices</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">View all invoices for your projects. Toggle currency to see amounts in TZS or USD.</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        display: 'original',
        invoices: @js($invoices->map(fn ($inv) => [
            'id' => $inv->id,
            'project' => $inv->project->title,
            'freelancer' => $inv->project->freelancer->name ?? 'N/A',
            'currency' => $inv->currency,
            'amount' => (float) $inv->total_amount,
            'amountTZS' => $inv->amountIn('TZS'),
            'amountUSD' => $inv->amountIn('USD'),
            'paidAmount' => (float) $inv->paid_amount,
            'paidTZS' => $inv->paidAmountIn('TZS'),
            'paidUSD' => $inv->paidAmountIn('USD'),
            'remainingTZS' => $inv->amountIn('TZS') - $inv->paidAmountIn('TZS'),
            'remainingUSD' => $inv->amountIn('USD') - $inv->paidAmountIn('USD'),
            'exchange_rate' => (float) ($inv->exchange_rate ?: \App\Models\Invoice::FALLBACK_RATE),
            'status' => $inv->status,
            'date' => $inv->created_at->format('M d, Y'),
        ])),
        get totalTZS() { return this.invoices.reduce((s, i) => s + i.amountTZS, 0); },
        get totalUSD() { return this.invoices.reduce((s, i) => s + i.amountUSD, 0); },
        get collectedTZS() { return this.invoices.reduce((s, i) => s + i.paidTZS, 0); },
        get collectedUSD() { return this.invoices.reduce((s, i) => s + i.paidUSD, 0); },
        get paidCount() { return this.invoices.filter(i => i.status === 'paid').length; },
        get partialCount() { return this.invoices.filter(i => i.status === 'partial').length; },
        get unpaidCount() { return this.invoices.filter(i => i.status === 'unpaid').length; },
        fmt(n, cur) {
            const sym = cur === 'USD' ? '$' : 'TZS ';
            return sym + n.toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        shownAmount(inv) {
            if (this.display === 'TZS') return this.fmt(inv.amountTZS, 'TZS');
            if (this.display === 'USD') return this.fmt(inv.amountUSD, 'USD');
            return inv.currency === 'USD' ? this.fmt(inv.amount, 'USD') : this.fmt(inv.amount, 'TZS');
        },
        shownPaid(inv) {
            if (this.display === 'TZS') return this.fmt(inv.paidTZS, 'TZS');
            if (this.display === 'USD') return this.fmt(inv.paidUSD, 'USD');
            return inv.currency === 'USD' ? this.fmt(inv.paidAmount, 'USD') : this.fmt(inv.paidAmount, 'TZS');
        },
        shownRemaining(inv) {
            const rem = inv.amount - inv.paidAmount;
            const remTZS = inv.amountTZS - inv.paidTZS;
            const remUSD = inv.amountUSD - inv.paidUSD;
            if (this.display === 'TZS') return this.fmt(remTZS, 'TZS');
            if (this.display === 'USD') return this.fmt(remUSD, 'USD');
            return inv.currency === 'USD' ? this.fmt(rem, 'USD') : this.fmt(rem, 'TZS');
        },
        shownConverted(inv) {
            if (this.display === 'TZS') return inv.currency !== 'TZS' ? '≈ ' + this.fmt(inv.amountUSD, 'USD') : '';
            if (this.display === 'USD') return inv.currency !== 'USD' ? '≈ ' + this.fmt(inv.amountTZS, 'TZS') : '';
            return inv.currency === 'USD' ? '≈ ' + this.fmt(inv.amountTZS, 'TZS') : '≈ ' + this.fmt(inv.amountUSD, 'USD');
        },
        statusClass(inv) {
            if (inv.status === 'paid') return 'border border-emerald-200 bg-emerald-50 text-emerald-700';
            if (inv.status === 'partial') return 'border border-blue-200 bg-blue-50 text-blue-700';
            return 'border border-amber-200 bg-amber-50 text-amber-700';
        },
        statusLabel(inv) {
            if (inv.status === 'partial') return 'Partial';
            return inv.status.charAt(0).toUpperCase() + inv.status.slice(1);
        }
    }">

        {{-- Currency Toggle --}}
        <div class="mb-6 flex flex-wrap items-center gap-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-brand-muted">Display as</span>
            <div class="inline-flex rounded-2xl border border-warm-300/50 bg-warm-100 p-1 shadow-sm">
                <button @click="display = 'original'" :class="display === 'original' ? 'bg-brand-primary text-white shadow-md' : 'text-brand-muted hover:text-brand-ink'"
                        class="rounded-xl px-4 py-2 text-xs font-semibold transition duration-200">
                    Original
                </button>
                <button @click="display = 'TZS'" :class="display === 'TZS' ? 'bg-brand-primary text-white shadow-md' : 'text-brand-muted hover:text-brand-ink'"
                        class="rounded-xl px-4 py-2 text-xs font-semibold transition duration-200">
                    🇹🇿 TZS
                </button>
                <button @click="display = 'USD'" :class="display === 'USD' ? 'bg-brand-primary text-white shadow-md' : 'text-brand-muted hover:text-brand-ink'"
                        class="rounded-xl px-4 py-2 text-xs font-semibold transition duration-200">
                    🇺🇸 USD
                </button>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card">
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-muted">Total</p>
                <p class="mt-1 font-display text-xl text-brand-ink" x-text="invoices.length"></p>
            </div>
            <div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card">
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-muted">Paid</p>
                <p class="mt-1 font-display text-xl text-emerald-600" x-text="paidCount"></p>
            </div>
            <div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card">
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-muted">Partial / Unpaid</p>
                <p class="mt-1 font-display text-xl text-amber-600" x-text="partialCount + unpaidCount"></p>
            </div>
            <div class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card">
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-muted"
                   x-text="display === 'USD' ? 'Collected (USD)' : 'Collected (TZS)'">Collected (TZS)</p>
                <p class="mt-1 font-display text-xl text-brand-primary"
                   x-text="display === 'USD' ? fmt(collectedUSD, 'USD') : fmt(collectedTZS, 'TZS')"></p>
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
                <template x-for="inv in invoices" :key="inv.id">
                    <div class="rounded-2xl border border-white/70 bg-white/90 p-5 shadow-card transition hover:shadow-panel">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0 flex-1 space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex rounded-lg border border-warm-300/40 bg-warm-200/50 px-2 py-0.5 text-[10px] font-bold text-brand-muted" x-text="'INV-' + String(inv.id).padStart(4, '0')"></span>
                                    <span class="inline-flex rounded-lg px-2 py-0.5 text-[10px] font-bold"
                                          :class="statusClass(inv)"
                                          x-text="statusLabel(inv)"></span>
                                    <span class="inline-flex rounded-lg border border-blue-100 bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-600" x-text="inv.currency"></span>
                                </div>
                                <p class="truncate font-display text-sm text-brand-ink" x-text="inv.project"></p>
                                <p class="text-xs text-brand-muted">
                                    Freelancer: <span x-text="inv.freelancer" class="font-medium"></span>
                                    &middot; <span x-text="inv.date"></span>
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="space-y-1">
                                    <div>
                                        <p class="text-[9px] font-bold uppercase tracking-wider text-brand-muted">Total</p>
                                        <p class="font-display text-base text-brand-ink" x-text="shownAmount(inv)"></p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-bold uppercase tracking-wider text-emerald-600">Paid</p>
                                        <p class="text-sm font-bold text-emerald-600" x-text="shownPaid(inv)"></p>
                                    </div>
                                    <template x-if="inv.status !== 'paid'">
                                        <div>
                                            <p class="text-[9px] font-bold uppercase tracking-wider text-red-500">Remaining</p>
                                            <p class="text-sm font-bold text-red-500" x-text="shownRemaining(inv)"></p>
                                        </div>
                                    </template>
                                </div>
                                <p class="mt-1 text-[10px] text-brand-muted/60" x-text="'Rate: 1 USD = ' + inv.exchange_rate.toLocaleString('en', {minimumFractionDigits: 2}) + ' TZS'"></p>
                            </div>
                            </div>
                        </div>
                        <div class="mt-3 border-t border-warm-300/40 pt-3">
                            <a :href="'{{ url('/invoices') }}/' + inv.id + '/pdf'" class="btn-secondary inline-flex w-full justify-center text-center text-xs">
                                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                Download Invoice (PDF)
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </div>
</x-app-layout>
