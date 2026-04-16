<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Plans</span>
            <h1 class="font-display text-3xl text-brand-ink">Create Plan</h1>
        </div>
    </x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('admin.subscription-plans.store') }}" class="space-y-6">
            @csrf

            <div class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-card space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Plan name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none focus:ring-1 focus:ring-brand-primary"
                           placeholder="e.g. Green Essential" />
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Colour tier</label>
                    <select name="color" required
                            class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        @foreach (['green' => 'Green Essential', 'blue' => 'Blue Advantage', 'purple' => 'Purple Elite', 'black' => 'Black Ultimate'] as $val => $label)
                            <option value="{{ $val }}" {{ old('color') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink mb-1.5">Monthly price (USD)</label>
                        <input type="number" name="price_monthly" value="{{ old('price_monthly', '0') }}" step="0.01" min="0" required
                               class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink mb-1.5">Yearly price (USD)</label>
                        <input type="number" name="price_yearly" value="{{ old('price_yearly', '0') }}" step="0.01" min="0" required
                               class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink mb-1.5">Features <span class="font-normal text-brand-muted">(one per line)</span></label>
                    <textarea name="features" rows="6"
                              class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none"
                              placeholder="Up to 3 active projects&#10;Basic file storage (1 GB)&#10;Email support">{{ old('features') }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink mb-1.5">Sort order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                               class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" />
                    </div>
                    <div class="flex items-end pb-0.5">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary" />
                            <span class="text-sm font-semibold text-brand-ink">Active (visible to clients)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="rounded-2xl bg-navy-800 px-6 py-2.5 text-sm font-semibold text-white shadow-card transition hover:bg-slate-800">
                    Create Plan
                </button>
                <a href="{{ route('admin.subscription-plans.index') }}"
                   class="rounded-2xl border border-warm-300/50 px-6 py-2.5 text-sm font-semibold text-brand-muted transition hover:text-brand-ink">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
