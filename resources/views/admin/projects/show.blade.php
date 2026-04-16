<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.projects.index') }}" class="rounded-2xl border border-warm-300/50 bg-warm-100 p-2 text-brand-muted transition hover:border-accent/30 hover:text-brand-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </a>
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Admin &middot; Project details
                </span>
            </div>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">{{ $project->title }}</h1>
                    <p class="text-sm text-brand-muted">Created {{ $project->created_at->format('M d, Y') }} by {{ $project->client->name }}</p>
                </div>
                <x-status-badge :status="$project->status" />
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        {{-- Chat Button --}}
        <a href="{{ route('chat.show', $project) }}" class="btn-primary inline-flex items-center gap-2 self-start">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
            Open Chat
        </a>

        {{-- Info Cards --}}
        <div class="grid gap-4 md:grid-cols-4">
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Client</p>
                <p class="mt-3 font-display text-xl text-brand-ink">{{ $project->client->name }}</p>
                <p class="mt-1 text-xs text-brand-muted">{{ $project->client->email }}</p>
            </div>
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Freelancer</p>
                <p class="mt-3 font-display text-xl text-brand-ink">{{ $project->freelancer?->name ?? 'Unassigned' }}</p>
            </div>
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Status</p>
                <div class="mt-3"><x-status-badge :status="$project->status" /></div>
            </div>
            <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                <p class="text-sm font-medium text-brand-muted">Files</p>
                <p class="mt-3 font-display text-xl text-brand-ink">{{ $project->files->count() }}</p>
            </div>
        </div>

        {{-- AI Freelancer Suggestions --}}
        @if ($aiSuggestions->isNotEmpty() && !$project->freelancer_id)
            <div class="rounded-3xl border border-accent/20 bg-gradient-to-br from-accent-light to-amber-50 p-6 shadow-panel">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-primary/10">
                        <svg class="h-4 w-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">AI Suggestions</p>
                        <p class="text-sm text-brand-muted">Best-matched freelancers for this project's categories</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($aiSuggestions as $i => $suggestion)
                        <div class="rounded-2xl border {{ $i === 0 ? 'border-accent/30 bg-white/90 ring-1 ring-accent/30' : 'border-white/80 bg-white/70' }} p-4">
                            @if ($i === 0)
                                <span class="inline-flex rounded-full bg-brand-primary/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-brand-primary">Top pick</span>
                            @endif
                            <p class="mt-2 font-semibold text-brand-ink">{{ $suggestion['freelancer']->name }}</p>
                            <p class="mt-1 text-xs text-brand-muted">{{ $suggestion['freelancer']->email }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <p class="text-xs leading-5 text-brand-muted">{{ $suggestion['reason'] }}</p>
                                <span class="ml-2 shrink-0 rounded-full border border-warm-300/50 bg-warm-200/50 px-2 py-0.5 text-[10px] font-bold text-brand-muted">
                                    Score {{ $suggestion['score'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Admin Actions --}}
        <div class="grid gap-6 xl:grid-cols-2">
            {{-- Assign Freelancer --}}
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Assign Freelancer</p>
                <form method="POST" action="{{ route('admin.projects.assign', $project) }}" class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end">
                    @csrf
                    @method('PATCH')
                    <div class="flex-1">
                        <label for="freelancer_id" class="block text-sm font-semibold text-brand-ink">Freelancer</label>
                        <select name="freelancer_id" id="freelancer_id"
                            class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">
                            <option value="">Select a freelancer…</option>
                            @foreach ($freelancers as $fl)
                                <option value="{{ $fl->id }}" {{ $project->freelancer_id == $fl->id ? 'selected' : '' }}>{{ $fl->name }} ({{ $fl->email }})</option>
                            @endforeach
                        </select>
                        @error('freelancer_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary shrink-0">Assign</button>
                </form>
            </div>

            {{-- Change Status --}}
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Change Status</p>
                <form method="POST" action="{{ route('admin.projects.status', $project) }}" class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end">
                    @csrf
                    @method('PATCH')
                    <div class="flex-1">
                        <label for="status" class="block text-sm font-semibold text-brand-ink">Status</label>
                        <select name="status" id="status"
                            class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">
                            @foreach ($statuses as $s)
                                <option value="{{ $s }}" {{ $project->status === $s ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary shrink-0">Update</button>
                </form>
            </div>
        </div>

        {{-- Freelancer Payment --}}
        @if ($project->freelancer_id)
            <div x-data="{ addPaymentOpen: false }" class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Freelancer Payment</p>
                    @if ($project->freelancerPayment)
                        @php $fp = $project->freelancerPayment; @endphp
                        @if ($fp->status !== 'paid')
                            <button @click="addPaymentOpen = true"
                                class="btn-primary shrink-0 self-start">+ Add Payment</button>
                        @endif
                    @endif
                </div>

                {{-- Set / Update Agreed Amount --}}
                <form method="POST" action="{{ route('admin.projects.freelancerPayment.set', $project) }}"
                      class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-brand-ink">
                            {{ $project->freelancerPayment ? 'Update Agreed Amount (TZS)' : 'Set Agreed Amount (TZS)' }}
                        </label>
                        <input type="number" name="agreed_amount" min="0.01" step="0.01"
                               value="{{ old('agreed_amount', $project->freelancerPayment?->agreed_amount) }}"
                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary"
                               placeholder="e.g. 500000" />
                        @error('agreed_amount')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary shrink-0">
                        {{ $project->freelancerPayment ? 'Update' : 'Set Amount' }}
                    </button>
                </form>

                {{-- Payment Summary --}}
                @if ($project->freelancerPayment)
                    @php $fp = $project->freelancerPayment; @endphp
                    <div class="mt-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-warm-300/40 bg-warm-200/50 px-4 py-4 text-center">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">Agreed</p>
                            <p class="mt-2 font-display text-xl text-brand-ink">{{ $fp->formattedAgreed() }}</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-4 text-center">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Paid</p>
                            <p class="mt-2 font-display text-xl text-emerald-700">{{ $fp->formattedPaid() }}</p>
                        </div>
                        <div class="rounded-2xl border border-accent/20 bg-accent-light px-4 py-4 text-center">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-primary">Remaining</p>
                            <p class="mt-2 font-display text-xl text-brand-primary">{{ $fp->formattedRemaining() }}</p>
                        </div>
                    </div>

                    {{-- Status badge --}}
                    <div class="mt-4 flex items-center gap-2">
                        <span class="text-sm text-brand-muted">Status:</span>
                        @php
                            $fpStatusClass = match($fp->status) {
                                'paid'    => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                'partial' => 'border-amber-200 bg-amber-50 text-amber-700',
                                default   => 'border-warm-300/50 bg-warm-200/50 text-warm-700',
                            };
                        @endphp
                        <span class="inline-flex rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $fpStatusClass }}">
                            {{ ucfirst($fp->status) }}
                        </span>
                    </div>

                    {{-- Payment Logs --}}
                    @if ($fp->logs->isNotEmpty())
                        <div class="mt-6">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">Payment History</p>
                            <div class="mt-3 space-y-2">
                                @foreach ($fp->logs->sortByDesc('created_at') as $log)
                                    <div class="flex items-center justify-between rounded-2xl border border-warm-300/40 bg-warm-200/60 px-4 py-3 text-sm">
                                        <span class="font-semibold text-brand-ink">TZS {{ number_format($log->amount, 2) }}</span>
                                        <span class="text-xs text-brand-muted">{{ $log->created_at->format('M d, Y  H:i') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            {{-- Add Payment Modal (inside same x-data scope) --}}
            @if ($project->freelancerPayment && $project->freelancerPayment->status !== 'paid')
                @php $fp = $project->freelancerPayment; @endphp
                <div x-show="addPaymentOpen"
                     x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4"
                     @keydown.escape.window="addPaymentOpen = false">
                    <div class="absolute inset-0 bg-navy-900/40 backdrop-blur-sm" @click="addPaymentOpen = false"></div>
                    <div class="relative w-full max-w-md rounded-3xl border border-white/70 bg-warm-100 p-7 shadow-panel"
                         x-transition:enter="transition duration-200 ease-out"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition duration-150 ease-in"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">
                        <h2 class="font-display text-xl text-brand-ink">Add Payment</h2>
                        <p class="mt-1 text-sm text-brand-muted">
                            Remaining: <span class="font-semibold text-brand-primary">{{ $fp->formattedRemaining() }}</span>
                        </p>
                        <form method="POST" action="{{ route('admin.freelancerPayments.addPayment', $fp) }}" class="mt-5 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Amount (TZS)</label>
                                <input type="number" name="amount" min="0.01"
                                       step="0.01"
                                       max="{{ number_format($fp->agreed_amount - $fp->paid_amount, 2, '.', '') }}"
                                       placeholder="e.g. 100000"
                                       class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary" />
                                @error('amount')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex gap-3 pt-2">
                                <button type="button"
                                        class="btn-secondary flex-1"
                                        @click="addPaymentOpen = false">Cancel</button>
                                <button type="submit" class="btn-primary flex-1">Record Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @else
            <div class="rounded-3xl border border-warm-300/50 bg-warm-200/50 p-6 shadow-panel">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-muted">Freelancer Payment</p>
                <p class="mt-3 text-sm text-brand-muted">Assign a freelancer to this project before setting a payment amount.</p>
            </div>
        @endif

        {{-- Description --}}
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Description</p>
            <div class="mt-4 text-sm leading-7 text-brand-muted whitespace-pre-line">{{ $project->description }}</div>
        </div>

        {{-- Files --}}
        @if ($project->files->isNotEmpty())
            <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Attachments</p>
                <div class="mt-4 space-y-2">
                    @foreach ($project->files as $file)
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                            class="flex items-center gap-3 rounded-2xl border border-warm-300/50 bg-warm-200/50 px-4 py-3 text-sm text-brand-ink transition duration-200 hover:border-accent/30 hover:bg-accent/10">
                            <svg class="h-5 w-5 shrink-0 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            <span class="truncate">{{ basename($file->file_path) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
