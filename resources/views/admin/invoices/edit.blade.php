<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.invoices') }}"
                   class="rounded-2xl border border-warm-300/50 bg-warm-100 p-2 text-brand-muted transition hover:border-accent/30 hover:text-brand-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </a>
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Edit invoice
                </span>
            </div>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">
                    Edit INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">
                    {{ $invoice->project->title }} &mdash; {{ $invoice->project->client->name ?? 'N/A' }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl space-y-5">

        {{-- Warning Banner --}}
        <div class="flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Editing invoice may affect payment records</p>
                <p class="mt-0.5 text-xs leading-5 text-amber-700">
                    Any change to the total amount or currency will recalculate the remaining balance.
                    The amount cannot be set below the already-paid amount
                    @if ($invoice->paid_amount > 0)
                        ({{ $invoice->formattedPaidAmount() }}).
                    @else
                        .
                    @endif
                </p>
            </div>
        </div>

        {{-- Payment Status Card (read-only) --}}
        @if ($invoice->paid_amount > 0 || $invoice->status !== 'unpaid')
            <div class="rounded-2xl border border-white/70 bg-white/90 px-5 py-4 shadow-card">
                <p class="mb-3 text-[10px] font-bold uppercase tracking-[0.2em] text-brand-muted">Current Payment Status</p>
                <div class="grid grid-cols-3 divide-x divide-warm-300/40">
                    <div class="pr-4">
                        <p class="text-[9px] font-bold uppercase tracking-wider text-brand-muted">Total</p>
                        <p class="mt-1 font-display text-base text-brand-ink">{{ $invoice->formattedAmount() }}</p>
                    </div>
                    <div class="px-4">
                        <p class="text-[9px] font-bold uppercase tracking-wider text-emerald-600">Paid</p>
                        <p class="mt-1 font-display text-base text-emerald-600">{{ $invoice->formattedPaidAmount() }}</p>
                    </div>
                    <div class="pl-4">
                        <p class="text-[9px] font-bold uppercase tracking-wider text-red-500">Remaining</p>
                        <p class="mt-1 font-display text-base text-red-500">{{ $invoice->formattedRemainingAmount() }}</p>
                    </div>
                </div>
                @if ($invoice->payments->isNotEmpty())
                    <div class="mt-4 border-t border-warm-300/40 pt-3">
                        <p class="mb-2 text-[9px] font-bold uppercase tracking-wider text-brand-muted">Payment History</p>
                        <div class="space-y-1.5">
                            @foreach ($invoice->payments as $payment)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-brand-muted">{{ $payment->created_at->format('M d, Y') }}</span>
                                    <span class="font-semibold text-emerald-600">
                                        {{ $invoice->currency === 'USD' ? '$' : 'TZS ' }}{{ number_format((float) $payment->amount, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Edit Form --}}
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8"
             x-data="{
                 currency: '{{ old('currency', $invoice->currency) }}',
                 amount: {{ old('amount', (float) $invoice->total_amount) }},
                 minAmount: {{ (float) $invoice->paid_amount > 0 ? (float) $invoice->paid_amount : 0.01 }},
                 rate: {{ \App\Models\Invoice::FALLBACK_RATE }},
                 get converted() {
                     if (!this.amount || this.amount <= 0) return null;
                     if (this.currency === 'USD') return this.amount * this.rate;
                     if (this.currency === 'TZS') return this.rate > 0 ? this.amount / this.rate : null;
                     return null;
                 },
                 get convertedLabel() {
                     if (!this.converted) return '';
                     if (this.currency === 'USD') return '≈ TZS ' + this.converted.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                     return '≈ $' + this.converted.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                 },
                 get belowPaid() {
                     return this.minAmount > 0.01 && this.amount < this.minAmount;
                 }
             }">

            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3">
                    <ul class="space-y-1 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.invoices.update', $invoice) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- Project (read-only) --}}
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Project</label>
                    <div class="mt-2 flex items-center gap-3 rounded-2xl border border-warm-300/50 bg-warm-200/50 px-4 py-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-warm-300">
                            <svg class="h-4 w-4 text-stone-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-brand-ink">{{ $invoice->project->title }}</p>
                            <p class="text-xs text-brand-muted">{{ $invoice->project->client->name ?? 'N/A' }}</p>
                        </div>
                        <span class="ml-auto text-[10px] font-bold uppercase tracking-wider text-stone-400">Read-only</span>
                    </div>
                </div>

                {{-- Currency --}}
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Currency</label>
                    <div class="mt-2 flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="currency" value="TZS" x-model="currency" class="peer sr-only">
                            <div class="flex items-center justify-center gap-2 rounded-2xl border-2 px-4 py-3 text-sm font-semibold transition duration-200
                                peer-checked:border-brand-primary peer-checked:bg-accent-light peer-checked:text-brand-primary
                                border-warm-300/50 text-brand-muted hover:border-warm-400/50">
                                <span class="text-lg">🇹🇿</span>
                                <span>TZS</span>
                                <span class="text-xs font-normal">(Tanzanian Shilling)</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="currency" value="USD" x-model="currency" class="peer sr-only">
                            <div class="flex items-center justify-center gap-2 rounded-2xl border-2 px-4 py-3 text-sm font-semibold transition duration-200
                                peer-checked:border-brand-primary peer-checked:bg-accent-light peer-checked:text-brand-primary
                                border-warm-300/50 text-brand-muted hover:border-warm-400/50">
                                <span class="text-lg">🇺🇸</span>
                                <span>USD</span>
                                <span class="text-xs font-normal">(US Dollar)</span>
                            </div>
                        </label>
                    </div>
                    @error('currency')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    @if ($invoice->paid_amount > 0 && $invoice->currency !== old('currency', $invoice->currency))
                        <p class="mt-2 text-xs text-amber-600">
                            Note: changing currency does not convert existing payment records. Paid amount ({{ $invoice->formattedPaidAmount() }}) will be treated as the new currency.
                        </p>
                    @endif
                </div>

                {{-- Amount --}}
                <div>
                    <label for="amount" class="block text-sm font-semibold text-brand-ink">Total Amount</label>
                    @if ($invoice->paid_amount > 0)
                        <p class="mt-0.5 text-xs text-brand-muted">
                            Minimum: <span class="font-semibold text-amber-600">{{ $invoice->formattedPaidAmount() }}</span> (already paid)
                        </p>
                    @endif
                    <div class="relative mt-2">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm text-brand-muted" x-text="currency === 'USD' ? '$' : 'TZS'"></span>
                        <input type="number" name="amount" id="amount"
                               step="0.01"
                               :min="minAmount"
                               x-model.number="amount"
                               placeholder="0.00"
                               class="w-full rounded-2xl border-warm-300/50 bg-warm-100 py-3 pr-4 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary"
                               :class="[currency === 'USD' ? 'pl-9' : 'pl-14', belowPaid ? 'border-red-400 ring-1 ring-red-300' : '']">
                    </div>
                    {{-- Inline warning if below paid --}}
                    <p x-show="belowPaid" x-cloak class="mt-2 text-sm text-red-500">
                        Amount cannot be less than the already-paid amount of
                        <span x-text="(currency === 'USD' ? '$' : 'TZS ') + minAmount.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>.
                    </p>
                    @error('amount')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Live Conversion Preview --}}
                <div x-show="amount > 0" x-cloak
                     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                     class="rounded-2xl border border-blue-100 bg-blue-50/50 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                        <p class="text-sm font-medium text-blue-700" x-text="convertedLabel"></p>
                    </div>
                    <p class="mt-1 text-[10px] text-blue-500/70">Rate: 1 USD = {{ number_format(\App\Models\Invoice::FALLBACK_RATE, 2) }} TZS (fallback &mdash; live rate fetched on save)</p>
                </div>

                {{-- Expiry Date --}}
                <div>
                    <label for="expires_at" class="block text-sm font-semibold text-brand-ink">
                        Expiry Date
                        <span class="ml-1 text-xs font-normal text-brand-muted">(optional)</span>
                    </label>
                    <input type="date" name="expires_at" id="expires_at"
                           value="{{ old('expires_at', $invoice->expires_at?->format('Y-m-d')) }}"
                           class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">
                    @error('expires_at')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    @if ($invoice->expires_at)
                        <p class="mt-1.5 text-xs text-brand-muted">
                            Currently set to: <span class="font-semibold">{{ $invoice->expires_at->format('M d, Y') }}</span>.
                            Clear the field to remove.
                        </p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 border-t border-warm-300/40 pt-6">
                    <button type="submit" class="btn-primary" :disabled="belowPaid" :class="belowPaid ? 'opacity-50 cursor-not-allowed' : ''">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9"/></svg>
                        Save Changes
                    </button>
                    <a href="{{ route('admin.invoices') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
