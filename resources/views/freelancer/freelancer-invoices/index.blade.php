<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
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
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Create Invoice</p>
            <form method="POST" action="{{ route('freelancer.freelancerInvoices.store') }}" class="mt-5 space-y-5">
                @csrf
                <div>
                    <label for="project_id" class="block text-sm font-semibold text-brand-ink">Project</label>
                    <select id="project_id" name="project_id" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary" required>
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
                    <label for="amount" class="block text-sm font-semibold text-brand-ink">Invoice Amount</label>
                    <input 
                        id="amount" 
                        name="amount" 
                        type="number" 
                        step="0.01" 
                        min="0.01" 
                        max="999999.99"
                        class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary"
                        value="{{ old('amount') }}"
                        placeholder="0.00"
                        required>
                    @error('amount')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-brand-ink">Description / Invoice Items</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary"
                        rows="4"
                        placeholder="List the work done, hours, rates, or detailed invoice items..."
                        required>{{ old('description') }}</textarea>
                    <p class="mt-2 text-xs text-brand-muted">Minimum 10 characters, describe the work or services provided.</p>
                    @error('description')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-semibold text-brand-ink">Due Date (Optional)</label>
                    <input 
                        id="due_date" 
                        name="due_date" 
                        type="date"
                        class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary"
                        value="{{ old('due_date') }}">
                    <p class="mt-2 text-xs text-brand-muted">When this invoice payment is due.</p>
                    @error('due_date')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full" {{ $projects->isEmpty() ? 'disabled' : '' }}>Create & Submit Invoice</button>
            </form>

            @if ($projects->isEmpty())
                <div class="mt-4 rounded-2xl border border-warm-300/50 bg-warm-200/50 px-4 py-3 text-sm text-brand-muted">
                    No projects are currently available. You need an assigned project to create an invoice.
                </div>
            @endif
        </div>

        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <div class="flex items-center justify-between gap-4">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Submitted Invoices</p>
                <span class="text-xs text-brand-muted">{{ $invoices->count() }} total</span>
            </div>

            @if ($invoices->isEmpty())
                <div class="mt-6 rounded-3xl border border-warm-300/40 bg-warm-200/50 p-10 text-center">
                    <p class="font-display text-xl text-brand-ink">No invoices created yet</p>
                    <p class="mt-2 text-sm text-brand-muted">Your submitted invoices will appear here with their approval status.</p>
                </div>
            @else
                <div class="mt-5 space-y-3">
                    @foreach ($invoices as $invoice)
                        <div class="rounded-2xl border border-warm-300/40 bg-warm-200/70 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="font-semibold text-brand-ink">{{ $invoice->project->title }}</p>
                                        <span class="inline-flex rounded-full bg-accent-light px-2 py-1 text-xs font-mono text-accent-hover">{{ $invoice->invoice_number }}</span>
                                    </div>
                                    <p class="mt-2 text-sm font-medium text-brand-ink">{{ number_format((float) $invoice->amount, 2) }} TZS</p>
                                    <p class="mt-1 text-xs text-brand-muted">Created {{ $invoice->created_at->format('M d, Y') }}</p>
                                    @if ($invoice->due_date)
                                        <p class="mt-1 text-xs text-brand-muted">Due: {{ $invoice->due_date->format('M d, Y') }}</p>
                                    @endif
                                </div>
                                <span class="inline-flex w-fit rounded-full border px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] {{ $invoice->statusClasses() }}">
                                    {{ $invoice->statusLabel() }}
                                </span>
                            </div>
                            
                            @if ($invoice->description)
                                <div class="mt-4 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink">
                                    <p class="font-semibold text-xs uppercase tracking-[0.08em] text-brand-muted">Description</p>
                                    <p class="mt-2 whitespace-pre-wrap leading-6">{{ $invoice->description }}</p>
                                </div>
                            @endif

                            @if ($invoice->rejection_note)
                                <div class="mt-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    <p class="font-semibold">Rejection reason</p>
                                    <p class="mt-2 leading-6">{{ $invoice->rejection_note }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
