<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Admin review
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Freelancer Invoices</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Review uploaded freelancer invoice PDFs and approve or reject them.</p>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($invoices->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
            <h3 class="font-display text-xl text-brand-ink">No freelancer invoices yet</h3>
            <p class="mt-2 text-sm text-brand-muted">Submitted invoices will appear here for approval.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($invoices as $invoice)
                <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-2">
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="font-display text-xl text-brand-ink">{{ $invoice->project->title }}</h3>
                                <span class="inline-flex rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] {{ $invoice->statusClasses() }}">
                                    {{ $invoice->statusLabel() }}
                                </span>
                            </div>
                            <p class="text-sm text-brand-muted">Freelancer: <span class="font-semibold text-brand-ink">{{ $invoice->freelancer->name }}</span></p>
                            <p class="text-xs text-brand-muted">Submitted {{ $invoice->created_at->format('M d, Y H:i') }}</p>
                            @if ($invoice->rejection_note)
                                <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    <span class="font-semibold">Rejection note:</span> {{ $invoice->rejection_note }}
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.freelancerInvoices.show', $invoice) }}" class="btn-secondary">Preview PDF</a>
                            @if ($invoice->status !== 'approved')
                                <form method="POST" action="{{ route('admin.freelancerInvoices.approve', $invoice) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Approve</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
