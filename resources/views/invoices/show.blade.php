<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <a href="{{ route($role . '.invoices') }}" class="rounded-2xl border border-warm-300/50 bg-warm-100 p-2 text-brand-muted transition hover:border-accent/30 hover:text-brand-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </a>
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Invoice details
                </span>
            </div>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</h1>
                    <p class="text-sm text-brand-muted">Project: {{ $invoice->project->title }}</p>
                </div>
                <div class="flex gap-2">
                    <span class="inline-flex rounded-full border border-warm-300/50 bg-warm-200/50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">{{ $invoice->currency }}</span>
                    <x-status-badge :status="$invoice->status" />
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[1.1fr,0.9fr]">
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Invoice Summary</p>
            <div class="mt-5 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-warm-300/40 bg-warm-200/50 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">Total</p>
                    <p class="mt-2 font-display text-xl text-brand-ink">{{ $invoice->formattedAmount() }}</p>
                </div>
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Paid</p>
                    <p class="mt-2 font-display text-xl text-emerald-700">{{ $invoice->formattedPaidAmount() }}</p>
                </div>
                <div class="rounded-2xl border border-accent/20 bg-accent-light px-4 py-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-primary">Remaining</p>
                    <p class="mt-2 font-display text-xl text-brand-primary">{{ $invoice->formattedRemainingAmount() }}</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">Client</p>
                    <p class="mt-2 text-sm font-semibold text-brand-ink">{{ $invoice->project->client->name }}</p>
                    <p class="text-sm text-brand-muted">{{ $invoice->project->client->email }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">Freelancer</p>
                    <p class="mt-2 text-sm font-semibold text-brand-ink">{{ $invoice->project->freelancer?->name ?? 'Unassigned' }}</p>
                    <p class="text-sm text-brand-muted">{{ $invoice->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            @if ($invoice->payments->isNotEmpty())
                <div class="mt-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">Payments</p>
                    <div class="mt-3 space-y-2">
                        @foreach ($invoice->payments->sortByDesc('created_at') as $payment)
                            <div class="flex items-center justify-between rounded-2xl border border-warm-300/40 bg-warm-200/35 px-4 py-3 text-sm">
                                <span class="font-semibold text-brand-ink">{{ $invoice->currency === 'USD' ? '$' : 'TZS ' }}{{ number_format($payment->amount, 2) }}</span>
                                <span class="text-brand-muted">{{ $payment->created_at->format('M d, Y H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Actions</p>
            <div class="mt-5 flex flex-col gap-3">
                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn-primary text-center">Download PDF</a>
                @if ($role === 'admin')
                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn-secondary text-center">Edit Invoice</a>
                @endif
                <a href="{{ route('projects.redirect', $invoice->project) }}" class="btn-secondary text-center">Open Project</a>
            </div>
        </div>
    </div>
</x-app-layout>
