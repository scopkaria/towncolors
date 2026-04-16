<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Admin
            </span>
            <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Edit Portfolio Item</h1>
        </div>
    </x-slot>

    <div class="mx-auto max-w-4xl rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
        <form method="POST" action="{{ route('admin.portfolio.update', $portfolio) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Title</label>
                    <input type="text" name="title" value="{{ old('title', $portfolio->title) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Client Name</label>
                    <input type="text" name="client_name" value="{{ old('client_name', $portfolio->client_name) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Live URL</label>
                    <input type="url" name="project_url" value="{{ old('project_url', $portfolio->project_url) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Industry</label>
                    <input type="text" name="industry" value="{{ old('industry', $portfolio->industry) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Country</label>
                    <input type="text" name="country" value="{{ old('country', $portfolio->country) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Completion Year</label>
                    <input type="number" name="completion_year" min="2000" max="2100" value="{{ old('completion_year', $portfolio->completion_year) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Duration</label>
                    <input type="text" name="duration" value="{{ old('duration', $portfolio->duration) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Status</label>
                    <select name="status" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                        @foreach (['pending', 'approved', 'rejected'] as $status)
                            <option value="{{ $status }}" {{ old('status', $portfolio->status) === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Type</label>
                    <select name="item_type" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                        <option value="project" {{ old('item_type', $portfolio->item_type ?? 'project') === 'project' ? 'selected' : '' }}>Project</option>
                        <option value="product" {{ old('item_type', $portfolio->item_type) === 'product' ? 'selected' : '' }}>Product</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Price</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $portfolio->price) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Currency</label>
                    <input type="text" name="currency" value="{{ old('currency', $portfolio->currency ?? 'USD') }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-brand-ink">Purchase URL</label>
                    <input type="url" name="purchase_url" value="{{ old('purchase_url', $portfolio->purchase_url) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-brand-ink">Services (comma separated)</label>
                    <input type="text" name="services" value="{{ old('services', implode(', ', $portfolio->services ?? [])) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-brand-ink">Technologies (comma separated)</label>
                    <input type="text" name="technologies" value="{{ old('technologies', implode(', ', $portfolio->technologies ?? [])) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-brand-ink">Description</label>
                <textarea name="description" rows="4" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">{{ old('description', $portfolio->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-brand-ink">Results</label>
                <textarea name="results" rows="4" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">{{ old('results', $portfolio->results) }}</textarea>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-brand-ink">
                    <input type="checkbox" name="featured" value="1" class="rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary" {{ old('featured', $portfolio->featured) ? 'checked' : '' }}>
                    Featured
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-brand-ink">
                    <input type="checkbox" name="is_purchasable" value="1" class="rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary" {{ old('is_purchasable', $portfolio->is_purchasable) ? 'checked' : '' }}>
                    Purchasable
                </label>
            </div>

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="flex items-center justify-between gap-3">
                <a href="{{ route('admin.portfolio.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
