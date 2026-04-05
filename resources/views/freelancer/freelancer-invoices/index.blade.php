<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Freelancer invoices
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Upload Invoices</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Submit PDF invoices for assigned projects. Files are stored privately and only accessible to you and admin.</p>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1.1fr,1.4fr]">
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">New Invoice</p>
            <form method="POST" action="{{ route('freelancer.freelancerInvoices.store') }}" enctype="multipart/form-data" class="mt-5 space-y-5">
                @csrf
                <div>
                    <label for="project_id" class="block text-sm font-semibold text-brand-ink">Project</label>
                    <select id="project_id" name="project_id" class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">
                        <option value="">Select a project...</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-brand-muted">Projects with an existing pending invoice are hidden until that invoice is approved or rejected.</p>
                    @error('project_id')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="invoice" class="block text-sm font-semibold text-brand-ink">Invoice PDF</label>
                    <label for="invoice" class="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-3xl border border-dashed border-orange-200 bg-orange-50/50 px-6 py-10 text-center transition hover:border-brand-primary hover:bg-orange-50">
                        <svg class="h-8 w-8 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 16.5V6.75m0 0-3 3m3-3 3 3M3 15.75v.75A2.25 2.25 0 0 0 5.25 18.75h13.5A2.25 2.25 0 0 0 21 16.5v-.75"/></svg>
                        <span class="mt-3 font-semibold text-brand-ink">Choose PDF file</span>
                        <span class="mt-1 text-xs text-brand-muted">PDF only, max 10MB</span>
                    </label>
                    <input id="invoice" name="invoice" type="file" accept="application/pdf" class="sr-only">
                    @error('invoice')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full" {{ $projects->isEmpty() ? 'disabled' : '' }}>Upload Invoice</button>
            </form>

            @if ($projects->isEmpty())
                <div class="mt-4 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-brand-muted">
                    No projects are currently available for a new invoice submission.
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <div class="flex items-center justify-between gap-4">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Submitted Invoices</p>
                <span class="text-xs text-brand-muted">{{ $invoices->count() }} total</span>
            </div>

            @if ($invoices->isEmpty())
                <div class="mt-6 rounded-3xl border border-stone-100 bg-stone-50 p-10 text-center">
                    <p class="font-display text-xl text-brand-ink">No invoices uploaded yet</p>
                    <p class="mt-2 text-sm text-brand-muted">Your submitted invoices will appear here with their approval status.</p>
                </div>
            @else
                <div class="mt-5 space-y-3">
                    @foreach ($invoices as $invoice)
                        <div class="rounded-2xl border border-stone-100 bg-stone-50/70 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-semibold text-brand-ink">{{ $invoice->project->title }}</p>
                                    <p class="mt-1 text-xs text-brand-muted">Submitted {{ $invoice->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <span class="inline-flex w-fit rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] {{ $invoice->statusClasses() }}">
                                    {{ $invoice->statusLabel() }}
                                </span>
                            </div>
                            @if ($invoice->rejection_note)
                                <div class="mt-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    <p class="font-semibold">Rejection reason</p>
                                    <p class="mt-2 leading-6">{{ $invoice->rejection_note }}</p>
                                </div>
                            @endif
                            <div class="mt-4 flex items-center justify-between gap-3 border-t border-stone-200 pt-3">
                                <span class="text-xs text-brand-muted">Stored securely</span>
                                <a href="{{ route('freelancerInvoices.download', $invoice) }}" class="inline-flex items-center gap-2 rounded-2xl border border-orange-200 bg-white px-4 py-2 text-xs font-semibold text-brand-primary transition hover:bg-brand-primary hover:text-white">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 16.5V3.75m0 12.75 4.5-4.5M12 16.5l-4.5-4.5M3 16.5v1.125c0 1.243 1.007 2.25 2.25 2.25h13.5c1.243 0 2.25-1.007 2.25-2.25V16.5"/></svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
