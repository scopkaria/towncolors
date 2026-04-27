<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Admin
                </span>
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Edit Portfolio Item</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Update project details, featured visuals, and gallery media from one screen.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('shop.index') }}" target="_blank" class="btn-secondary">Shop Index</a>
                <a href="{{ route('portfolio.show', $portfolio) }}" target="_blank" class="btn-secondary">Preview Live</a>
                <a href="{{ route('admin.portfolio.index') }}" class="btn-secondary">Back to Portfolio</a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl">
        <form method="POST" action="{{ route('admin.portfolio.update', $portfolio) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                <div class="space-y-6">
                    <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                        <div class="mb-5 flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-brand-primary">Core Details</p>
                                <h2 class="mt-2 font-display text-2xl text-brand-ink">Project Information</h2>
                            </div>
                            <span class="rounded-full border border-warm-300/60 bg-warm-100 px-3 py-1 text-xs font-semibold text-brand-muted">#{{ $portfolio->id }}</span>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold text-brand-ink">Title</label>
                                <input type="text" name="title" value="{{ old('title', $portfolio->title) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Client Name</label>
                                <input type="text" name="client_name" value="{{ old('client_name', $portfolio->client_name) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
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
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold text-brand-ink">Duration</label>
                                <input type="text" name="duration" value="{{ old('duration', $portfolio->duration) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                            </div>
                        </div>

                        <div class="mt-5 grid gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Description</label>
                                <textarea name="description" rows="4" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">{{ old('description', $portfolio->description) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-brand-ink">Results</label>
                                <textarea name="results" rows="4" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">{{ old('results', $portfolio->results) }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                        <div class="mb-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-brand-primary">Commerce</p>
                            <h2 class="mt-2 font-display text-2xl text-brand-ink">Links, Pricing, and Metadata</h2>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
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
                                <label class="block text-sm font-semibold text-brand-ink">Live URL</label>
                                <input type="url" name="project_url" value="{{ old('project_url', $portfolio->project_url) }}" placeholder="https://example.com/project" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
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
                                <label class="block text-sm font-semibold text-brand-ink">Purchase Link</label>
                                <input type="text" name="purchase_url" value="{{ old('purchase_url', $portfolio->purchase_url) }}" placeholder="https://checkout.example.com or mailto:sales@example.com" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                                <p class="mt-2 text-xs text-brand-muted">Supports standard URLs plus `mailto:` and `tel:` links.</p>
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
                    </section>
                </div>

                <div class="space-y-6">
                    <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                        <div class="mb-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-brand-primary">Media</p>
                            <h2 class="mt-2 font-display text-2xl text-brand-ink">Featured Image & Gallery</h2>
                        </div>

                        <div class="rounded-2xl border border-warm-300/50 bg-warm-100 p-4">
                            <label class="block text-sm font-semibold text-brand-ink">Featured Image</label>
                            <div class="mt-3 space-y-4">
                                <div class="overflow-hidden rounded-2xl border border-warm-300/50 bg-white">
                                    <div class="flex aspect-[4/3] items-center justify-center bg-warm-200/60">
                                        @if ($portfolio->image_path)
                                            <img src="{{ asset('storage/' . $portfolio->image_path) }}" alt="{{ $portfolio->title }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-[11px] font-semibold uppercase tracking-wide text-brand-muted">No featured image</span>
                                        @endif
                                    </div>
                                </div>
                                <input type="file" name="image" accept="image/*" class="w-full rounded-2xl border border-warm-300/50 bg-white px-4 py-2.5 text-sm text-brand-ink file:mr-3 file:rounded-xl file:border-0 file:bg-accent-light file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-primary">
                                @if ($portfolio->image_path)
                                    <label class="inline-flex items-center gap-2 text-sm text-brand-ink">
                                        <input type="checkbox" name="remove_featured_image" value="1" class="rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                                        Remove current featured image
                                    </label>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl border border-warm-300/50 bg-warm-100 p-4">
                            <label class="block text-sm font-semibold text-brand-ink">Add Gallery Images</label>
                            <p class="mt-1 text-xs text-brand-muted">Upload one or more screenshots, previews, or supporting visuals.</p>
                            <input type="file" name="gallery_uploads[]" accept="image/*" multiple class="mt-3 w-full rounded-2xl border border-warm-300/50 bg-white px-4 py-2.5 text-sm text-brand-ink file:mr-3 file:rounded-xl file:border-0 file:bg-accent-light file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-primary">
                        </div>

                        <div class="mt-5 rounded-2xl border border-warm-300/50 bg-warm-100 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <label class="block text-sm font-semibold text-brand-ink">Current Gallery</label>
                                <span class="text-xs text-brand-muted">{{ count($portfolio->product_gallery ?? []) }} items</span>
                            </div>

                            @if (! empty($portfolio->product_gallery))
                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    @foreach (($portfolio->product_gallery ?? []) as $galleryPath)
                                        <label class="overflow-hidden rounded-2xl border border-warm-300/50 bg-white">
                                            <div class="aspect-square overflow-hidden bg-warm-200/60">
                                                <img src="{{ asset('storage/' . $galleryPath) }}" alt="Gallery image" class="h-full w-full object-cover">
                                            </div>
                                            <div class="flex items-center gap-2 px-3 py-2">
                                                <input type="checkbox" name="remove_gallery[]" value="{{ $galleryPath }}" class="rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary">
                                                <span class="text-xs font-medium text-brand-muted">Remove</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-3 text-sm text-brand-muted">No gallery images added yet.</p>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel sm:p-8">
                        <div class="mb-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-brand-primary">Visibility</p>
                            <h2 class="mt-2 font-display text-2xl text-brand-ink">Publish Options</h2>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-start gap-3 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink">
                                <input type="checkbox" name="featured" value="1" class="mt-1 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary" {{ old('featured', $portfolio->featured) ? 'checked' : '' }}>
                                <span>
                                    <span class="block font-semibold">Featured item</span>
                                    <span class="mt-1 block text-xs text-brand-muted">Highlight this entry in prominent public placements.</span>
                                </span>
                            </label>
                            <label class="flex items-start gap-3 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink">
                                <input type="checkbox" name="is_purchasable" value="1" class="mt-1 rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary" {{ old('is_purchasable', $portfolio->is_purchasable) ? 'checked' : '' }}>
                                <span>
                                    <span class="block font-semibold">Purchasable</span>
                                    <span class="mt-1 block text-xs text-brand-muted">Show purchase actions and pricing on the public product page.</span>
                                </span>
                            </label>
                        </div>
                    </section>
                </div>
            </div>

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="sticky bottom-4 z-10 rounded-3xl border border-white/70 bg-white/95 p-4 shadow-panel backdrop-blur">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-brand-muted">Save when you're done updating the content and media.</p>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.portfolio.index') }}" class="btn-secondary">Cancel</a>
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
