<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.freelancerInvoices.index') }}" class="rounded-2xl border border-warm-300/50 bg-warm-100 p-2 text-brand-muted transition hover:border-accent/30 hover:text-brand-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </a>
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Admin · Invoice preview
                </span>
            </div>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">{{ $freelancerInvoice->project->title }}</h1>
                <p class="text-sm text-brand-muted">Submitted by {{ $freelancerInvoice->freelancer->name }} on {{ $freelancerInvoice->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[320px,1fr]">
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] {{ $freelancerInvoice->statusClasses() }}">
                    {{ $freelancerInvoice->statusLabel() }}
                </span>
            </div>
            <div class="mt-5 space-y-3 text-sm text-brand-muted">
                <p>Invoice: <span class="font-semibold text-brand-ink">{{ $freelancerInvoice->invoice_number ?? ('INV-' . str_pad((string) $freelancerInvoice->id, 5, '0', STR_PAD_LEFT)) }}</span></p>
                <p>Freelancer: <span class="font-semibold text-brand-ink">{{ $freelancerInvoice->freelancer->name }}</span></p>
                <p>Project: <span class="font-semibold text-brand-ink">{{ $freelancerInvoice->project->title }}</span></p>
                <p>Amount: <span class="font-semibold text-brand-ink">{{ number_format((float) ($freelancerInvoice->amount ?? 0), 2) }} TZS</span></p>
                @if ($freelancerInvoice->due_date)
                    <p>Due date: <span class="font-semibold text-brand-ink">{{ $freelancerInvoice->due_date->format('M d, Y') }}</span></p>
                @endif
            </div>
            @if ($freelancerInvoice->description)
                <div class="mt-5 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-4 text-sm text-brand-ink">
                    <p class="font-semibold">Description</p>
                    <p class="mt-2 whitespace-pre-wrap leading-6">{{ $freelancerInvoice->description }}</p>
                </div>
            @endif
            @if ($freelancerInvoice->rejection_note)
                <div class="mt-5 rounded-2xl border border-red-100 bg-red-50 px-4 py-4 text-sm text-red-700">
                    <p class="font-semibold">Current rejection note</p>
                    <p class="mt-2 leading-6">{{ $freelancerInvoice->rejection_note }}</p>
                </div>
            @endif
            <div class="mt-6 flex flex-col gap-3">
                @if ($freelancerInvoice->status !== 'approved')
                    <form method="POST" action="{{ route('admin.freelancerInvoices.approve', $freelancerInvoice) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Approve</button>
                    </form>
                @endif
                @if ($freelancerInvoice->status !== 'rejected')
                    <form method="POST" action="{{ route('admin.freelancerInvoices.reject', $freelancerInvoice) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="rejection_note" class="block text-sm font-semibold text-brand-ink">Rejection note</label>
                            <textarea id="rejection_note" name="rejection_note" rows="4" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary" placeholder="Explain what needs to be corrected before resubmission...">{{ old('rejection_note') }}</textarea>
                            @error('rejection_note')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-red-700">Reject</button>
                    </form>
                @endif
                <a href="{{ route('freelancerInvoices.download', $freelancerInvoice) }}" class="btn-secondary text-center">Download PDF</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-panel">
            <iframe src="{{ route('freelancerInvoices.file', $freelancerInvoice) }}" class="h-[80vh] w-full" title="Freelancer invoice preview"></iframe>
        </div>
    </div>
</x-app-layout>
