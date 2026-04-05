<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.invoices') }}"
                   class="rounded-2xl border border-stone-200 bg-white p-2 text-brand-muted transition hover:border-orange-200 hover:text-brand-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                </a>
                <span class="inline-flex rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Create invoice
                </span>
            </div>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">New Invoice</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Select a project, choose currency, and enter the amount.</p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
            @if ($projects->isEmpty())
                <div class="py-8 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-stone-100">
                        <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </div>
                    <h3 class="mt-4 font-display text-lg text-brand-ink">All projects invoiced</h3>
                    <p class="mt-2 text-sm text-brand-muted">Every project already has an invoice.</p>
                    <a href="{{ route('admin.invoices') }}" class="btn-secondary mt-5 inline-flex">Back to Invoices</a>
                </div>
            @else
                <form method="POST" action="{{ route('admin.invoices.store') }}" class="space-y-6"
                      x-data="{
                          currency: '{{ old('currency', 'TZS') }}',
                          amount: {{ old('amount', 0) }},
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
                          }
                      }">
                    @csrf

                    {{-- Project Select --}}
                    <div>
                        <label for="project_id" class="block text-sm font-semibold text-brand-ink">Project</label>
                        <select name="project_id" id="project_id"
                                class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary">
                            <option value="">Select a project&hellip;</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->title }} &mdash; {{ $project->client->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Currency --}}
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Currency</label>
                        <div class="mt-2 flex gap-3">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="currency" value="TZS" x-model="currency" class="peer sr-only">
                                <div class="flex items-center justify-center gap-2 rounded-2xl border-2 px-4 py-3 text-sm font-semibold transition duration-200
                                    peer-checked:border-brand-primary peer-checked:bg-orange-50 peer-checked:text-brand-primary
                                    border-stone-200 text-brand-muted hover:border-stone-300">
                                    <span class="text-lg">🇹🇿</span>
                                    <span>TZS</span>
                                    <span class="text-xs font-normal">(Tanzanian Shilling)</span>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="currency" value="USD" x-model="currency" class="peer sr-only">
                                <div class="flex items-center justify-center gap-2 rounded-2xl border-2 px-4 py-3 text-sm font-semibold transition duration-200
                                    peer-checked:border-brand-primary peer-checked:bg-orange-50 peer-checked:text-brand-primary
                                    border-stone-200 text-brand-muted hover:border-stone-300">
                                    <span class="text-lg">🇺🇸</span>
                                    <span>USD</span>
                                    <span class="text-xs font-normal">(US Dollar)</span>
                                </div>
                            </label>
                        </div>
                        @error('currency')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label for="amount" class="block text-sm font-semibold text-brand-ink">Amount</label>
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm text-brand-muted" x-text="currency === 'USD' ? '$' : 'TZS'"></span>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                                   x-model.number="amount"
                                   placeholder="0.00"
                                   class="w-full rounded-2xl border-stone-200 bg-white py-3 pr-4 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary"
                                   :class="currency === 'USD' ? 'pl-9' : 'pl-14'">
                        </div>
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
                        <p class="mt-1 text-[10px] text-blue-500/70">Rate: 1 USD = {{ number_format(\App\Models\Invoice::FALLBACK_RATE, 2) }} TZS (fallback &mdash; live rate fetched on submit)</p>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center gap-3 border-t border-stone-100 pt-6">
                        <button type="submit" class="btn-primary">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Create Invoice
                        </button>
                        <a href="{{ route('admin.invoices') }}" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
