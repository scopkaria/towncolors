<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Admin Shop
            </span>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Shop Products</h1>
                    <p class="max-w-2xl text-sm leading-7 text-brand-muted">Add custom products with full information, pricing, and purchase links. These appear automatically on the public shop page.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.shop.requests.index') }}" class="btn-secondary inline-flex items-center gap-2">
                        View Shop Requests
                    </a>
                    <a href="{{ route('shop.index') }}" target="_blank" class="btn-secondary inline-flex items-center gap-2">
                        View Frontend Shop
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-8 xl:grid-cols-[420px,1fr]">
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel"
             x-data="window.shopProductForm()">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Add New Product</p>

            <form method="POST" action="{{ route('admin.shop.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Product Name</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Description</label>
                    <textarea name="description" rows="4" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Full Product Description</label>
                    <textarea name="product_description" rows="6" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary" placeholder="Detailed product explanation for the single product page">{{ old('product_description') }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Price</label>
                        <input type="number" name="price" min="0" step="0.01" value="{{ old('price') }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', 'USD') }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Industry</label>
                        <input type="text" name="industry" value="{{ old('industry') }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Completion Year</label>
                        <input type="number" name="completion_year" min="2000" max="2100" value="{{ old('completion_year', now()->year) }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Purchase URL</label>
                    <input type="url" name="purchase_url" value="{{ old('purchase_url') }}" placeholder="https://checkout.example.com/product" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Website URL (Optional)</label>
                    <input type="url" name="project_url" value="{{ old('project_url') }}" placeholder="https://example.com" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Services (comma separated)</label>
                    <input type="text" name="services" value="{{ old('services') }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary" placeholder="Billing, User Management, Reports">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Technologies (comma separated)</label>
                    <input type="text" name="technologies" value="{{ old('technologies') }}" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary" placeholder="Laravel, Vue, MySQL">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Results / Value</label>
                    <textarea name="results" rows="3" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">{{ old('results') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Image</label>
                    <input type="file" name="image" accept="image/*" class="mt-2 w-full rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm text-brand-ink file:mr-3 file:rounded-xl file:border-0 file:bg-accent-light file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-primary">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Or Select Featured Image From Media Library</label>
                    <input type="hidden" name="image_media_id" :value="featuredId">
                    <div class="mt-2 rounded-2xl border border-warm-300/50 bg-warm-100 p-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl border border-warm-300/50 bg-warm-200/50">
                                <template x-if="featuredMedia">
                                    <img :src="featuredMedia.url" :alt="featuredMedia.name" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!featuredMedia">
                                    <span class="text-[11px] font-semibold uppercase tracking-wide text-brand-muted">None</span>
                                </template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Featured image</p>
                                <p class="truncate text-sm text-brand-ink" x-text="featuredMedia ? featuredMedia.name : 'No featured media selected'"></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="btn-secondary" @click="openMediaModal('single')">Choose</button>
                                <button type="button" class="btn-secondary" x-show="featuredId" @click="clearFeatured()">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Gallery Images (Media Library Multi-Select)</label>
                    <template x-for="id in galleryIds" :key="`gallery-input-${id}`">
                        <input type="hidden" name="gallery_media_ids[]" :value="id">
                    </template>
                    <div class="mt-2 rounded-2xl border border-warm-300/50 bg-warm-100 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm text-brand-muted">
                                <span class="font-semibold text-brand-ink" x-text="selectedGalleryMedia.length"></span>
                                selected
                            </p>
                            <button type="button" class="btn-secondary" @click="openMediaModal('multi')">Manage Gallery</button>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-4" x-show="selectedGalleryMedia.length">
                            <template x-for="item in selectedGalleryMedia" :key="`gallery-item-${item.id}`">
                                <div class="group relative aspect-square overflow-hidden rounded-xl border border-warm-300/50">
                                    <img :src="item.url" :alt="item.name" class="h-full w-full object-cover">
                                    <button type="button" class="absolute right-1 top-1 rounded-full bg-black/60 p-1 text-white opacity-0 transition group-hover:opacity-100" @click="removeGallery(item.id)">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <p class="mt-2 text-xs text-brand-muted" x-show="!selectedGalleryMedia.length">No gallery media selected yet.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Gallery Uploads (New Files)</label>
                    <input type="file" name="gallery_uploads[]" accept="image/*" multiple class="mt-2 w-full rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm text-brand-ink file:mr-3 file:rounded-xl file:border-0 file:bg-accent-light file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-primary">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Extra Info</label>
                    <textarea name="extra_info" rows="3" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary" placeholder="Licensing terms, support policy, onboarding details">{{ old('extra_info') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <label class="inline-flex items-center gap-2 rounded-xl border border-warm-300/50 bg-warm-200/60 px-3 py-2 text-sm text-brand-ink">
                        <input type="checkbox" name="featured" value="1" class="rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary" {{ old('featured') ? 'checked' : '' }}>
                        Featured
                    </label>
                    <label class="inline-flex items-center gap-2 rounded-xl border border-warm-300/50 bg-warm-200/60 px-3 py-2 text-sm text-brand-ink">
                        <input type="checkbox" name="is_purchasable" value="1" class="rounded border-warm-400/50 text-brand-primary focus:ring-brand-primary" {{ old('is_purchasable', true) ? 'checked' : '' }}>
                        Purchasable
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Status</label>
                    <select name="status" class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                        <option value="approved" {{ old('status', 'approved') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                @if ($errors->any())
                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div x-show="mediaModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-navy-900/60 p-4 backdrop-blur-sm" @keydown.escape.window="closeMediaModal()">
                    <div class="flex h-[80vh] w-[78vw] max-w-6xl flex-col rounded-3xl border border-white/70 bg-warm-100 shadow-panel" @click.outside="closeMediaModal()">
                        <div class="flex items-center justify-between border-b border-warm-300/40 px-6 py-4">
                            <div>
                                <h3 class="font-display text-lg text-brand-ink" x-text="mediaModalMode === 'single' ? 'Select featured image' : 'Manage gallery images'"></h3>
                                <p class="text-xs text-brand-muted">Use Media Library or upload new files, then click Use Selected.</p>
                            </div>
                            <button type="button" class="rounded-xl p-2 text-brand-muted hover:bg-warm-200 hover:text-brand-ink" @click="closeMediaModal()">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <div class="border-b border-warm-300/40 px-6 py-3">
                            <div class="inline-flex rounded-xl border border-warm-300/50 bg-warm-100 p-1">
                                <button type="button" class="rounded-lg px-3 py-1.5 text-xs font-semibold" :class="mediaModalTab === 'library' ? 'bg-brand-primary text-white' : 'text-brand-ink'" @click="mediaModalTab = 'library'">Select from Library</button>
                                <button type="button" class="rounded-lg px-3 py-1.5 text-xs font-semibold" :class="mediaModalTab === 'upload' ? 'bg-brand-primary text-white' : 'text-brand-ink'" @click="mediaModalTab = 'upload'">Upload New</button>
                            </div>
                        </div>

                        <div class="min-h-0 flex-1 p-6">
                            <div x-show="mediaModalTab === 'library'" class="flex h-full min-h-0 flex-col">
                                <input type="text" x-model="mediaQuery" placeholder="Search media files..." class="mb-4 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:ring-brand-primary">
                                <div class="min-h-0 flex-1 overflow-y-auto">
                                    <template x-if="filteredMedia.length === 0">
                                        <p class="py-10 text-center text-sm text-brand-muted">No media found for your search.</p>
                                    </template>
                                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5" x-show="filteredMedia.length">
                                        <template x-for="item in filteredMedia" :key="`media-${item.id}`">
                                            <button
                                                type="button"
                                                class="group relative overflow-hidden rounded-2xl border-2 bg-warm-100 text-left transition"
                                                :class="isMediaSelected(item.id) ? 'border-brand-primary ring-2 ring-brand-primary/30' : 'border-warm-300/50 hover:border-brand-primary/40'"
                                                @click="toggleMediaSelection(item.id)">
                                                <div class="aspect-square overflow-hidden bg-warm-200">
                                                    <img :src="item.url" :alt="item.name" class="h-full w-full object-cover transition duration-200 group-hover:scale-105">
                                                </div>
                                                <div class="px-2.5 py-2">
                                                    <p class="truncate text-xs font-semibold text-brand-ink" x-text="item.name"></p>
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div x-show="mediaModalTab === 'upload'" class="h-full">
                                <div class="rounded-2xl border-2 border-dashed border-warm-300/60 bg-warm-100 p-8 text-center">
                                    <p class="text-sm font-semibold text-brand-ink">Upload files to Media Library</p>
                                    <p class="mt-1 text-xs text-brand-muted">Multiple uploads supported. First upload is auto-selected.</p>
                                    <label class="btn-primary mt-4 inline-flex cursor-pointer">
                                        Choose Files
                                        <input type="file" class="sr-only" multiple @change="uploadFromModal($event)">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-warm-300/40 px-6 py-4">
                            <a href="{{ route('admin.media.index') }}" target="_blank" class="text-sm text-brand-primary hover:underline">Open full Media Library</a>
                            <button type="button" class="btn-primary" :disabled="mediaSelection.length === 0" @click="useSelectedMedia()">Use Selected</button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full">Add Product to Shop</button>
            </form>
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Shop Listings</p>
                <span class="text-xs text-brand-muted">{{ $products->count() }} products</span>
            </div>

            @if ($products->isEmpty())
                <div class="mt-5 rounded-3xl border border-dashed border-warm-300/50 bg-white/70 p-12 text-center">
                    <h3 class="font-display text-xl text-brand-ink">No shop products yet</h3>
                    <p class="mt-2 text-sm text-brand-muted">Use the form to add your first product and publish it to the frontend shop page.</p>
                </div>
            @else
                <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($products as $product)
                        <div class="overflow-hidden rounded-3xl border border-warm-300/40 bg-warm-100 shadow-card">
                            <div class="relative h-40 overflow-hidden bg-gradient-to-br from-emerald-50 to-accent-light">
                                @if ($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="h-full w-full object-cover">
                                @endif
                                <span class="absolute left-3 top-3 rounded-full bg-emerald-500/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white">{{ ucfirst($product->status) }}</span>
                            </div>
                            <div class="p-4">
                                <p class="text-sm font-semibold text-brand-ink line-clamp-2">{{ $product->title }}</p>
                                <p class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-brand-primary">{{ $product->currency ?? 'USD' }} {{ number_format((float) ($product->price ?? 0), 2) }}</p>
                                @if ($product->description)
                                    <p class="mt-2 text-xs leading-6 text-brand-muted line-clamp-3">{{ $product->description }}</p>
                                @endif
                                <div class="mt-3 flex items-center justify-between gap-2">
                                    <a href="{{ route('admin.portfolio.edit', $product) }}" class="text-xs font-semibold text-brand-primary hover:underline">Edit</a>
                                    <a href="{{ route('shop.show', $product) }}" target="_blank" class="text-xs font-semibold text-emerald-600 hover:underline">View</a>
                                    <form method="POST" action="{{ route('admin.portfolio.destroy', $product) }}" onsubmit="return confirm('Delete this product permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-red-500 hover:underline">Delete</button>
                                    </form>
                                    <span class="text-[10px] text-brand-muted">{{ $product->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
    window.shopProductForm = function () {
        return {
            mediaModalOpen: false,
            mediaModalMode: 'single',
            mediaModalTab: 'library',
            mediaQuery: '',
            mediaSelection: [],
            mediaUploading: false,
            mediaItems: @js($imageMedia->map(fn ($image) => [
                'id' => $image->id,
                'name' => $image->file_name,
                'url' => Storage::disk('public')->url($image->file_path),
            ])->values()),
            featuredId: '{{ old('image_media_id', '') }}',
            galleryIds: @js(array_values((array) old('gallery_media_ids', []))),
            openMediaModal(mode) {
                this.mediaModalMode = mode;
                this.mediaModalOpen = true;
                this.mediaQuery = '';
                this.mediaModalTab = 'library';
                if (mode === 'single') {
                    this.mediaSelection = this.featuredId ? [String(this.featuredId)] : [];
                } else {
                    this.mediaSelection = [...this.galleryIds];
                }
            },
            closeMediaModal() {
                this.mediaModalOpen = false;
            },
            isMediaSelected(id) {
                return this.mediaSelection.includes(String(id));
            },
            toggleMediaSelection(id) {
                const value = String(id);
                if (this.mediaModalMode === 'single') {
                    this.mediaSelection = [value];
                    return;
                }

                if (this.mediaSelection.includes(value)) {
                    this.mediaSelection = this.mediaSelection.filter(item => item !== value);
                    return;
                }

                this.mediaSelection.push(value);
            },
            useSelectedMedia() {
                if (this.mediaSelection.length === 0) return;

                if (this.mediaModalMode === 'single') {
                    this.featuredId = this.mediaSelection[0];
                } else {
                    this.galleryIds = [...new Set(this.mediaSelection)];
                }

                this.mediaModalOpen = false;
            },
            async uploadFromModal(event) {
                const files = event.target.files;
                if (!files || files.length === 0) return;

                this.mediaUploading = true;
                try {
                    const form = new FormData();
                    Array.from(files).forEach(file => form.append('files[]', file));

                    const res = await fetch('{{ route('admin.media.api.upload') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: form,
                    });

                    if (!res.ok) {
                        throw new Error('Upload failed');
                    }

                    const payload = await res.json();
                    const uploaded = payload.items || [];
                    if (uploaded.length > 0) {
                        this.mediaItems = [...uploaded, ...this.mediaItems.filter(item => !uploaded.some(newItem => String(newItem.id) === String(item.id)))];
                        this.mediaSelection = [String(uploaded[0].id)];
                        this.mediaModalTab = 'library';
                    }
                } catch (e) {
                    alert('Upload failed. Please try again.');
                } finally {
                    this.mediaUploading = false;
                    event.target.value = '';
                }
            },
            isGallerySelected(id) {
                return this.galleryIds.includes(String(id));
            },
            toggleGallery(id) {
                const value = String(id);
                if (this.galleryIds.includes(value)) {
                    this.galleryIds = this.galleryIds.filter(item => item !== value);
                    return;
                }
                this.galleryIds.push(value);
            },
            removeGallery(id) {
                const value = String(id);
                this.galleryIds = this.galleryIds.filter(item => item !== value);
            },
            clearFeatured() {
                this.featuredId = '';
            },
            get filteredMedia() {
                const q = this.mediaQuery.trim().toLowerCase();
                if (!q) return this.mediaItems;
                return this.mediaItems.filter(item => item.name.toLowerCase().includes(q));
            },
            get featuredMedia() {
                return this.mediaItems.find(item => String(item.id) === String(this.featuredId));
            },
            get selectedGalleryMedia() {
                return this.mediaItems.filter(item => this.galleryIds.includes(String(item.id)));
            }
        };
    };
</script>
