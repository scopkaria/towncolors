<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Financial management
            </span>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Invoice Dashboard</h1>
                    <p class="max-w-2xl text-sm leading-7 text-brand-muted">Financial analytics, tracking, and management for all invoices.</p>
                </div>
                <a href="{{ route('admin.invoices.create') }}" class="btn-primary shrink-0">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Invoice
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Analytics Summary Cards --}}
    @php
        $totalAmount     = $invoices->sum(fn ($inv) => $inv->amountIn('TZS'));
        $collectedAmount = $invoices->sum(fn ($inv) => $inv->paidAmountIn('TZS'));
        $remainingAmount = $totalAmount - $collectedAmount;
        $totalCount      = $invoices->count();
        $paidCount       = $invoices->where('status', 'paid')->count();
        $partialCount    = $invoices->where('status', 'partial')->count();
        $unpaidCount     = $invoices->where('status', 'unpaid')->count();
        $overdueInvoices = $invoices->whereIn('status', ['unpaid', 'partial'])->filter(fn ($inv) => $inv->expires_at && $inv->expires_at->isPast());
        $overdueCount    = $overdueInvoices->count();
        $overdueAmount   = $overdueInvoices->sum(fn ($inv) => $inv->amountIn('TZS') - $inv->paidAmountIn('TZS'));
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Number of Invoices --}}
        <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-50">
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Total Invoices</p>
                    <p class="font-display text-2xl text-brand-ink">{{ $totalCount }}</p>
                </div>
            </div>
            <div class="mt-3 flex gap-3 border-t border-warm-300/40 pt-3">
                <span class="text-xs text-emerald-600"><span class="font-bold">{{ $paidCount }}</span> paid</span>
                <span class="text-xs text-blue-600"><span class="font-bold">{{ $partialCount }}</span> partial</span>
                <span class="text-xs text-amber-600"><span class="font-bold">{{ $unpaidCount }}</span> unpaid</span>
            </div>
        </div>

        {{-- Total Collected (green) --}}
        <div class="card-premium rounded-3xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-card">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-100">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-700">Total Collected</p>
                    <p class="font-display text-2xl text-emerald-700">TZS {{ number_format($collectedAmount, 0) }}</p>
                </div>
            </div>
            <p class="mt-3 border-t border-emerald-200/50 pt-3 text-xs text-emerald-600">
                ≈ ${{ number_format($invoices->sum(fn ($inv) => $inv->paidAmountIn('USD')), 2) }} USD
            </p>
        </div>

        {{-- Remaining Payments (orange) --}}
        <div class="card-premium rounded-3xl border border-amber-100 bg-amber-50/60 p-5 shadow-card">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-amber-700">Remaining Balance</p>
                    <p class="font-display text-2xl text-amber-700">TZS {{ number_format($remainingAmount, 0) }}</p>
                </div>
            </div>
            <p class="mt-3 border-t border-amber-200/50 pt-3 text-xs text-amber-600">
                {{ $unpaidCount + $partialCount }} invoice{{ ($unpaidCount + $partialCount) !== 1 ? 's' : '' }} with balance due
            </p>
        </div>

        {{-- Overdue Invoices (red) --}}
        <div class="card-premium rounded-3xl border border-red-100 bg-red-50/60 p-5 shadow-card">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-red-100">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-red-700">Overdue</p>
                    <p class="font-display text-2xl text-red-700">{{ $overdueCount }}</p>
                </div>
            </div>
            <p class="mt-3 border-t border-red-200/50 pt-3 text-xs text-red-600">
                @if ($overdueCount > 0)
                    TZS {{ number_format($overdueAmount, 0) }} overdue
                @else
                    No overdue invoices
                @endif
            </p>
        </div>
    </div>

    {{-- Total Invoiced Amount --}}
    <div class="rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Total Invoiced Amount</p>
                <p class="mt-1 font-display text-3xl text-brand-ink">TZS {{ number_format($totalAmount, 0) }}</p>
                <p class="mt-1 text-xs text-brand-muted">≈ ${{ number_format($invoices->sum(fn ($inv) => $inv->amountIn('USD')), 2) }} USD</p>
            </div>
            <div class="flex gap-6 text-center">
                <div>
                    <p class="font-display text-2xl text-emerald-600">{{ $totalAmount > 0 ? round(($collectedAmount / $totalAmount) * 100) : 0 }}%</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-brand-muted">Collected rate</p>
                </div>
                <div class="border-l border-warm-300/40 pl-6">
                    <p class="font-display text-2xl text-brand-primary">TZS {{ number_format($invoices->where('currency', 'TZS')->sum('amount'), 0) }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-brand-muted">TZS invoiced</p>
                </div>
                <div class="border-l border-warm-300/40 pl-6">
                    <p class="font-display text-2xl text-brand-primary">${{ number_format($invoices->where('currency', 'USD')->sum('amount'), 2) }}</p>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-brand-muted">USD invoiced</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
        <form method="GET" action="{{ route('admin.invoices') }}" class="space-y-4">
            <div class="flex items-center gap-2">
                <svg class="h-4 w-4 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z"/></svg>
                <span class="text-xs font-bold uppercase tracking-[0.24em] text-brand-muted">Filters</span>
            </div>
            <div class="flex flex-wrap items-end gap-3">
                {{-- Status --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-brand-muted">Status</label>
                    <select name="status" onchange="this.form.submit()"
                            class="rounded-xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-xs font-semibold text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        <option value="">All status</option>
                        <option value="unpaid" {{ ($filters['status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ ($filters['status'] ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ ($filters['status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                {{-- Currency --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-brand-muted">Currency</label>
                    <select name="currency" onchange="this.form.submit()"
                            class="rounded-xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-xs font-semibold text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        <option value="">All currencies</option>
                        <option value="TZS" {{ ($filters['currency'] ?? '') === 'TZS' ? 'selected' : '' }}>TZS</option>
                        <option value="USD" {{ ($filters['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD</option>
                    </select>
                </div>

                {{-- Client --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-brand-muted">Client</label>
                    <select name="client" onchange="this.form.submit()"
                            class="rounded-xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-xs font-semibold text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        <option value="">All clients</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" {{ ($filters['client'] ?? '') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-brand-muted">From</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" onchange="this.form.submit()"
                           class="rounded-xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-xs font-semibold text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="mb-1 block text-[10px] font-bold uppercase tracking-wider text-brand-muted">To</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" onchange="this.form.submit()"
                           class="rounded-xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-xs font-semibold text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                </div>

                @if (array_filter($filters))
                    <a href="{{ route('admin.invoices') }}" class="rounded-xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-xs font-semibold text-brand-muted transition hover:border-red-200 hover:text-red-500">
                        Clear all
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Invoice Table --}}
    @if ($invoices->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-accent-light">
                <svg class="h-8 w-8 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
            </div>
            <h3 class="mt-4 font-display text-xl text-brand-ink">No invoices found</h3>
            <p class="mt-2 text-sm text-brand-muted">
                @if (array_filter($filters))
                    No invoices match your current filters.
                @else
                    Create an invoice for a project to get started.
                @endif
            </p>
            @unless (array_filter($filters))
                <a href="{{ route('admin.invoices.create') }}" class="btn-primary mt-5 inline-flex">Create Invoice</a>
            @endunless
        </div>
    @else
        <div class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-warm-300/40 bg-warm-200/70">
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">#</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Client</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Project</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Total / Paid / Remaining</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Currency</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Status</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Date</th>
                            <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-300/40">
                        @foreach ($invoices as $invoice)
                            @php
                                $isOverdue = $invoice->status === 'unpaid' && $invoice->expires_at && $invoice->expires_at->isPast();
                            @endphp
                            <tr class="transition duration-150 hover:bg-accent/10 {{ $isOverdue ? 'bg-red-50/30' : '' }}">
                                {{-- Invoice # --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-lg border border-warm-300/40 bg-warm-200/50 px-2 py-0.5 text-[10px] font-bold text-brand-muted">
                                        INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                {{-- Client --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-semibold text-brand-ink">{{ $invoice->project->client->name ?? 'N/A' }}</p>
                                    @if ($invoice->project->client->email ?? null)
                                        <p class="text-[10px] text-brand-muted">{{ $invoice->project->client->email }}</p>
                                    @endif
                                </td>

                                {{-- Project --}}
                                <td class="max-w-[200px] px-5 py-4">
                                    <p class="truncate text-sm font-medium text-brand-ink">{{ $invoice->project->title }}</p>
                                    @if ($invoice->project->freelancer)
                                        <p class="text-[10px] text-brand-muted">{{ $invoice->project->freelancer->name }}</p>
                                    @endif
                                </td>

                                {{-- Amount --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-bold text-brand-ink">{{ $invoice->formattedAmount() }}</p>
                                    @if ($invoice->status !== 'unpaid')
                                        <p class="mt-0.5 text-[10px] font-semibold text-emerald-600">Paid: {{ $invoice->formattedPaidAmount() }}</p>
                                    @endif
                                    @if ($invoice->status === 'partial')
                                        <p class="text-[10px] font-semibold text-red-500">Left: {{ $invoice->formattedRemainingAmount() }}</p>
                                    @endif
                                    @if ($invoice->converted_amount)
                                        <p class="mt-0.5 text-[10px] text-brand-muted">
                                            ≈ {{ $invoice->currency === 'USD' ? 'TZS ' . number_format($invoice->converted_amount, 2) : '$' . number_format($invoice->converted_amount, 2) }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Currency --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex rounded-lg border border-blue-100 bg-blue-50 px-2.5 py-0.5 text-[10px] font-bold text-blue-600">
                                        {{ $invoice->currency }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($isOverdue)
                                        <span class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-red-600">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>
                                            Overdue
                                        </span>
                                    @elseif ($invoice->status === 'paid')
                                        <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-600">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                            Paid
                                        </span>
                                    @elseif ($invoice->status === 'partial')
                                        <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-blue-600">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/></svg>
                                            Partial
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-amber-600">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                            Unpaid
                                        </span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm text-brand-ink">{{ $invoice->created_at->format('M d, Y') }}</p>
                                    @if ($invoice->expires_at)
                                        <p class="text-[10px] {{ $isOverdue ? 'font-bold text-red-500' : 'text-brand-muted' }}">
                                            Expires: {{ $invoice->expires_at->format('M d, Y') }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($invoice->status !== 'paid')
                                            @php $remaining = (float) $invoice->total_amount - (float) $invoice->paid_amount; @endphp
                                            <div x-data="{ open: false }">
                                                <button @click="open = true" title="Add Payment"
                                                        class="inline-flex h-8 items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 text-[10px] font-bold text-emerald-700 transition hover:bg-emerald-100">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                                    Payment
                                                </button>

                                                <template x-teleport="body">
                                                    <div x-show="open" x-transition class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none;">
                                                        <div class="absolute inset-0 bg-navy-900/50 backdrop-blur-sm" @click="open = false"></div>
                                                        <div class="relative z-10 w-full max-w-md rounded-3xl border border-white/70 bg-warm-100 p-6 shadow-2xl">
                                                            {{-- Header --}}
                                                            <div class="mb-5 flex items-start justify-between gap-3">
                                                                <div>
                                                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Record Payment</p>
                                                                    <h3 class="mt-1 font-display text-xl text-brand-ink">
                                                                        INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}
                                                                    </h3>
                                                                    <p class="text-xs text-brand-muted">{{ $invoice->project->title }} &mdash; {{ $invoice->project->client->name ?? '' }}</p>
                                                                </div>
                                                                <button @click="open = false" class="mt-1 rounded-xl p-1.5 text-brand-muted transition hover:bg-warm-200 hover:text-brand-ink">
                                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                                                </button>
                                                            </div>

                                                            {{-- Breakdown --}}
                                                            <div class="mb-5 grid grid-cols-3 gap-3 rounded-2xl bg-warm-200/50 p-4">
                                                                <div class="text-center">
                                                                    <p class="text-[9px] font-bold uppercase tracking-wider text-brand-muted">Total</p>
                                                                    <p class="mt-1 text-sm font-bold text-brand-ink">{{ $invoice->formattedAmount() }}</p>
                                                                </div>
                                                                <div class="border-x border-warm-300/50 text-center">
                                                                    <p class="text-[9px] font-bold uppercase tracking-wider text-brand-muted">Paid</p>
                                                                    <p class="mt-1 text-sm font-bold text-emerald-600">{{ $invoice->formattedPaidAmount() }}</p>
                                                                </div>
                                                                <div class="text-center">
                                                                    <p class="text-[9px] font-bold uppercase tracking-wider text-brand-muted">Remaining</p>
                                                                    <p class="mt-1 text-sm font-bold text-red-500">{{ $invoice->formattedRemainingAmount() }}</p>
                                                                </div>
                                                            </div>

                                                            {{-- Form --}}
                                                            <form method="POST" action="{{ route('admin.invoices.addPayment', $invoice) }}">
                                                                @csrf
                                                                <div class="space-y-4">
                                                                    <div>
                                                                        <label class="block text-xs font-semibold text-brand-ink">
                                                                            Amount ({{ $invoice->currency }})
                                                                        </label>
                                                                        <input type="number" name="amount"
                                                                               step="0.01" min="0.01" max="{{ number_format($remaining, 2, '.', '') }}"
                                                                               placeholder="0.00"
                                                                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary"
                                                                               required>
                                                                        <p class="mt-1.5 text-[10px] text-brand-muted">
                                                                            Max: {{ $invoice->formattedRemainingAmount() }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="flex gap-3 pt-1">
                                                                        <button type="button" @click="open = false"
                                                                                class="btn-secondary flex-1">
                                                                            Cancel
                                                                        </button>
                                                                        <button type="submit" class="btn-primary flex-1">
                                                                            Record Payment
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        @endif
                                        <a href="{{ route('admin.invoices.edit', $invoice) }}" title="Edit Invoice"
                                           class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-warm-300/50 bg-warm-100 text-brand-muted transition hover:border-blue-200 hover:text-blue-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                        </a>
                                        <a href="{{ route('invoices.pdf', $invoice) }}" title="Download PDF"
                                           class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-warm-300/50 bg-warm-100 text-brand-muted transition hover:border-brand-primary hover:text-brand-primary">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Table Footer Summary --}}
            <div class="flex flex-wrap items-center justify-between gap-4 border-t border-warm-300/40 bg-warm-200/50 px-5 py-4">
                <p class="text-xs text-brand-muted">
                    Showing <span class="font-bold text-brand-ink">{{ $invoices->count() }}</span> invoice{{ $invoices->count() !== 1 ? 's' : '' }}
                    @if (array_filter($filters))
                        <span class="text-brand-muted">(filtered)</span>
                    @endif
                </p>
                <div class="flex gap-4 text-xs">
                    <span class="text-emerald-600">Collected: <span class="font-bold">TZS {{ number_format($collectedAmount, 0) }}</span></span>
                    <span class="text-amber-600">Remaining: <span class="font-bold">TZS {{ number_format($remainingAmount, 0) }}</span></span>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
