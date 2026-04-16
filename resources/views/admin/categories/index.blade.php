<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Configuration
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Project Categories</h1>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Define service types with optional subcategories. Subcategories inherit their parent's structure.</p>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- ── Create Form ── --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <h2 class="font-display text-lg text-brand-ink">Add Category</h2>
                <p class="mt-1 text-xs text-brand-muted">Leave "Parent" empty to create a top-level category.</p>

                <form method="POST" action="{{ route('admin.categories.store') }}"
                      enctype="multipart/form-data"
                      class="mt-6 space-y-4"
                      x-data="{ preview: null, color: '{{ old('color', '#FFB162') }}' }">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-brand-ink">Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name') }}"
                               placeholder="e.g. Web Development"
                               class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-semibold text-brand-ink">Description</label>
                        <textarea name="description" id="description" rows="2"
                                  placeholder="Short description…"
                                  class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Long description --}}
                    <div>
                        <label for="long_description" class="block text-sm font-semibold text-brand-ink">
                            Full Page Description
                            <span class="ml-1 text-xs font-normal text-brand-muted">(shown on service page)</span>
                        </label>
                        <textarea name="long_description" id="long_description" rows="4"
                                  placeholder="Detailed description for the public service page…"
                                  class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ old('long_description') }}</textarea>
                        @error('long_description')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pricing & duration --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="price_range" class="block text-sm font-semibold text-brand-ink">Price Range</label>
                            <input type="text" name="price_range" id="price_range"
                                   value="{{ old('price_range') }}"
                                   placeholder="e.g. $500 – $2,000"
                                   class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            @error('price_range')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="estimated_duration" class="block text-sm font-semibold text-brand-ink">Estimated Duration</label>
                            <input type="text" name="estimated_duration" id="estimated_duration"
                                   value="{{ old('estimated_duration') }}"
                                   placeholder="e.g. 2 – 4 weeks"
                                   class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            @error('estimated_duration')
                                <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Parent category --}}
                    <div>
                        <label for="parent_id" class="block text-sm font-semibold text-brand-ink">Parent Category</label>
                        <select name="parent_id" id="parent_id"
                                class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                            <option value="">— None (top-level) —</option>
                            @foreach ($rootCategories as $root)
                                <option value="{{ $root->id }}" {{ old('parent_id') == $root->id ? 'selected' : '' }}>
                                    {{ $root->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Image upload --}}
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Thumbnail Image</label>
                        <div class="mt-2">
                            <label class="group flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-warm-300/50 bg-warm-200/50 px-4 py-5 text-center transition hover:border-brand-primary hover:bg-accent/10"
                                   x-show="!preview">
                                <svg class="h-8 w-8 text-brand-muted/50 transition group-hover:text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                </svg>
                                <span class="text-xs text-brand-muted">Click to upload (max 2 MB)</span>
                                <input type="file" name="image" accept="image/*" class="hidden"
                                       @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                            </label>
                            <div x-show="preview" x-cloak class="relative mt-2">
                                <img :src="preview" alt="Preview" class="h-32 w-full rounded-2xl object-cover">
                                <button type="button" @click="preview = null; $el.closest('div').previousElementSibling.querySelector('input').value = ''"
                                        class="absolute right-2 top-2 rounded-full bg-white/90 p-1 shadow-sm transition hover:bg-red-50 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        @error('image')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Featured image --}}
                    <div x-data="{ featPreview: null }">
                        <label class="block text-sm font-semibold text-brand-ink">
                            Featured Image
                            <span class="ml-1 text-xs font-normal text-brand-muted">(hero on service page, recommended 1200×600)</span>
                        </label>
                        <div class="mt-2">
                            <label class="group flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-warm-300/50 bg-warm-200/50 px-4 py-5 text-center transition hover:border-brand-primary hover:bg-accent/10"
                                   x-show="!featPreview">
                                <svg class="h-8 w-8 text-brand-muted/50 transition group-hover:text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5"/>
                                </svg>
                                <span class="text-xs text-brand-muted">Click to upload (max 4 MB)</span>
                                <input type="file" name="featured_image" accept="image/*" class="hidden"
                                       @change="featPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                            </label>
                            <div x-show="featPreview" x-cloak class="relative mt-2">
                                <img :src="featPreview" alt="Featured preview" class="h-40 w-full rounded-2xl object-cover">
                                <button type="button" @click="featPreview = null"
                                        class="absolute right-2 top-2 rounded-full bg-white/90 p-1 shadow-sm transition hover:bg-red-50 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                        @error('featured_image')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">
                            Service Gallery
                            <span class="ml-1 text-xs font-normal text-brand-muted">(up to 8 images)</span>
                        </label>
                        <input type="file" name="gallery_images[]" accept="image/*" multiple
                               class="mt-2 w-full rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-xs text-brand-muted shadow-sm">
                        @error('gallery_images')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        @error('gallery_images.*')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Color --}}
                    <div>
                        <label class="block text-sm font-semibold text-brand-ink">Badge Color <span class="text-red-400">*</span></label>
                        <div class="mt-2 flex items-center gap-3">
                            <input type="color" name="color" id="color"
                                   x-model="color"
                                   class="h-10 w-14 cursor-pointer rounded-xl border border-warm-300/50 p-0.5 shadow-sm">
                            <span class="flex-1 rounded-2xl px-4 py-2 text-xs font-bold text-white transition"
                                  :style="'background-color: ' + color"
                                  x-text="color"></span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach (['#FFB162','#3B82F6','#10B981','#8B5CF6','#EC4899','#EAB308','#EF4444','#06B6D4','#64748B'] as $preset)
                                <button type="button"
                                        @click="color = '{{ $preset }}'; document.getElementById('color').value = '{{ $preset }}'"
                                        class="h-6 w-6 rounded-full border-2 border-white shadow-sm transition hover:scale-110"
                                        style="background-color: {{ $preset }}"
                                        title="{{ $preset }}"></button>
                            @endforeach
                        </div>
                        @error('color')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary w-full">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add Category
                    </button>
                </form>
            </div>
        </div>

        {{-- ── Category Tree ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Stats bar --}}
            @php
                $totalRoot = $rootCategories->count();
                $totalSub  = $rootCategories->sum(fn ($c) => $c->children->count());
                $totalProj = $rootCategories->sum(fn ($c) => $c->projects_count + $c->children->sum('projects_count'));
            @endphp
            <div class="grid grid-cols-3 gap-3">
                @foreach ([['Top-level', $totalRoot, 'text-brand-primary', 'bg-accent-light border-accent/20'], ['Subcategories', $totalSub, 'text-violet-600', 'bg-violet-50 border-violet-100'], ['Projects', $totalProj, 'text-emerald-600', 'bg-emerald-50 border-emerald-100']] as [$lbl, $val, $tc, $bc])
                    <div class="rounded-2xl border {{ $bc }} px-4 py-3 text-center">
                        <p class="font-display text-2xl font-bold {{ $tc }}">{{ $val }}</p>
                        <p class="mt-0.5 text-[10px] font-semibold uppercase tracking-wider text-brand-muted">{{ $lbl }}</p>
                    </div>
                @endforeach
            </div>

            @if ($rootCategories->isEmpty())
                <div class="rounded-3xl border border-white/70 bg-white/90 p-12 text-center shadow-panel">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-warm-200">
                        <svg class="h-8 w-8 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" d="M6 6h.008v.008H6V6Z"/></svg>
                    </div>
                    <h3 class="mt-4 font-display text-lg text-brand-ink">No categories yet</h3>
                    <p class="mt-2 text-sm text-brand-muted">Add your first category using the form on the left.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($rootCategories as $root)
                        {{-- ── Root Category Card ── --}}
                        <div class="overflow-hidden rounded-3xl border border-white/70 bg-white/90 shadow-panel"
                             x-data="{ editing: false, color: '{{ $root->color }}', preview: null }">

                            {{-- View row --}}
                            <div x-show="!editing" class="flex items-start gap-4 p-5">
                                {{-- Image or colour swatch --}}
                                <div class="shrink-0">
                                    @if ($root->image_path)
                                        <img src="{{ asset('storage/' . $root->image_path) }}"
                                             alt="{{ $root->name }}"
                                             class="h-14 w-14 rounded-2xl object-cover shadow-sm">
                                    @else
                                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl text-xl font-bold text-white shadow-sm"
                                              style="background-color: {{ $root->color }}">
                                            {{ strtoupper(substr($root->name, 0, 1)) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-display text-lg text-brand-ink">{{ $root->name }}</p>
                                        <span class="inline-flex rounded-full border px-2.5 py-0.5 text-[10px] font-bold text-white"
                                              style="background-color: {{ $root->color }}">
                                            {{ $root->name }}
                                        </span>
                                        <span class="rounded-full bg-warm-200 px-2 py-0.5 text-[10px] font-semibold text-brand-muted">
                                            {{ $root->projects_count }} project{{ $root->projects_count !== 1 ? 's' : '' }}
                                        </span>
                                        @if ($root->children->count())
                                            <span class="rounded-full bg-violet-100 px-2 py-0.5 text-[10px] font-semibold text-violet-700">
                                                {{ $root->children->count() }} sub{{ $root->children->count() !== 1 ? 'categories' : 'category' }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($root->description)
                                        <p class="mt-1 text-sm leading-6 text-brand-muted">{{ $root->description }}</p>
                                    @endif
                                </div>

                                <div class="flex shrink-0 items-center gap-2">
                                    <button type="button" @click="editing = true"
                                            class="rounded-xl border border-warm-300/50 bg-warm-100 p-2 text-brand-muted transition hover:border-brand-primary hover:text-brand-primary">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $root) }}"
                                          onsubmit="return confirm('Delete \'{{ addslashes($root->name) }}\'? Sub-categories will be promoted to top-level.')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="rounded-xl border border-warm-300/50 bg-warm-100 p-2 text-brand-muted transition hover:border-red-200 hover:text-red-500">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Edit row --}}
                            <div x-show="editing" x-cloak class="border-t border-warm-300/40 bg-warm-200/60 px-5 py-5">
                                <form method="POST" action="{{ route('admin.categories.update', $root) }}"
                                      enctype="multipart/form-data" class="space-y-4">
                                    @csrf @method('PATCH')

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="block text-xs font-semibold text-brand-muted">Name</label>
                                            <input type="text" name="name" value="{{ $root->name }}" required
                                                   class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-brand-muted">Badge Color</label>
                                            <div class="mt-1 flex items-center gap-2">
                                                <input type="color" name="color" x-model="color"
                                                       class="h-9 w-12 cursor-pointer rounded-lg border border-warm-300/50 p-0.5 shadow-sm">
                                                <span class="flex-1 rounded-lg px-3 py-2 text-xs font-bold text-white transition"
                                                      :style="'background-color: ' + color" x-text="color"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-brand-muted">Description</label>
                                        <textarea name="description" rows="2"
                                                  class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ $root->description }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-brand-muted">
                                            Full Page Description
                                            <span class="ml-1 font-normal text-brand-muted/70">(service page)</span>
                                        </label>
                                        <textarea name="long_description" rows="3"
                                                  class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ $root->long_description }}</textarea>
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-2">
                                        <div>
                                            <label class="block text-xs font-semibold text-brand-muted">Price Range</label>
                                            <input type="text" name="price_range" value="{{ $root->price_range }}"
                                                   placeholder="e.g. $500 – $2,000"
                                                   class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-brand-muted">Estimated Duration</label>
                                            <input type="text" name="estimated_duration" value="{{ $root->estimated_duration }}"
                                                   placeholder="e.g. 2 – 4 weeks"
                                                   class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-brand-muted">Parent Category</label>
                                        <select name="parent_id"
                                                class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-2 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                                            <option value="">— None (top-level) —</option>
                                            @foreach ($rootCategories as $opt)
                                                @if ($opt->id !== $root->id)
                                                    <option value="{{ $opt->id }}" {{ $root->parent_id == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-brand-muted">Replace Thumbnail</label>
                                        <div class="mt-1 flex items-center gap-3">
                                            @if ($root->image_path)
                                                <img src="{{ asset('storage/' . $root->image_path) }}"
                                                     alt="Current" class="h-10 w-10 rounded-xl object-cover shadow-sm">
                                            @endif
                                            <input type="file" name="image" accept="image/*"
                                                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                                                   class="flex-1 rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs text-brand-muted shadow-sm">
                                        </div>
                                        <img x-show="preview" :src="preview" alt="New preview"
                                             class="mt-2 h-20 w-full rounded-xl object-cover" x-cloak>
                                    </div>

                                    <div x-data="{ featPreview: null }">
                                        <label class="block text-xs font-semibold text-brand-muted">Replace Featured Image</label>
                                        <div class="mt-1 flex items-center gap-3">
                                            @if ($root->featured_image)
                                                <img src="{{ asset('storage/' . $root->featured_image) }}"
                                                     alt="Current featured" class="h-10 w-20 rounded-xl object-cover shadow-sm">
                                            @endif
                                            <input type="file" name="featured_image" accept="image/*"
                                                   @change="featPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                                                   class="flex-1 rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs text-brand-muted shadow-sm">
                                        </div>
                                        <img x-show="featPreview" :src="featPreview" alt="New featured preview"
                                             class="mt-2 h-28 w-full rounded-xl object-cover" x-cloak>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-brand-muted">Replace Service Gallery</label>
                                        @if (!empty($root->gallery_images))
                                            <div class="mt-2 grid grid-cols-3 gap-2">
                                                @foreach ($root->gallery_images as $galleryImage)
                                                    <img src="{{ asset('storage/' . $galleryImage) }}" alt="Gallery image" class="h-16 w-full rounded-lg object-cover">
                                                @endforeach
                                            </div>
                                        @endif
                                        <input type="file" name="gallery_images[]" accept="image/*" multiple
                                               class="mt-2 w-full rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-2 text-xs text-brand-muted shadow-sm">
                                    </div>

                                    <div class="flex gap-3">
                                        <button type="submit" class="btn-primary py-2 text-xs">Save changes</button>
                                        <button type="button" @click="editing = false; preview = null" class="btn-secondary py-2 text-xs">Cancel</button>
                                    </div>
                                </form>
                            </div>

                            {{-- ── Subcategory list ── --}}
                            @if ($root->children->count())
                                <div class="border-t border-warm-300/40 bg-warm-200/40 px-5 py-3">
                                    <p class="mb-3 flex items-center gap-1.5 text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-muted">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m9 13.5 3 3m0 0 3-3m-3 3v-6m1.06-4.19-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/></svg>
                                        Subcategories
                                    </p>
                                    <div class="grid gap-2 sm:grid-cols-2">
                                        @foreach ($root->children as $child)
                                            <div class="flex items-center gap-3 rounded-2xl border border-warm-300/40 bg-warm-100 px-4 py-3 shadow-sm"
                                                 x-data="{ editing: false, color: '{{ $child->color }}', preview: null }">

                                                {{-- View --}}
                                                <div x-show="!editing" class="flex w-full items-center gap-3">
                                                    @if ($child->image_path)
                                                        <img src="{{ asset('storage/' . $child->image_path) }}"
                                                             alt="{{ $child->name }}"
                                                             class="h-9 w-9 shrink-0 rounded-xl object-cover shadow-sm">
                                                    @else
                                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-xs font-bold text-white shadow-sm"
                                                              style="background-color: {{ $child->color }}">
                                                            {{ strtoupper(substr($child->name, 0, 1)) }}
                                                        </span>
                                                    @endif
                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate text-sm font-semibold text-brand-ink">{{ $child->name }}</p>
                                                        @if ($child->description)
                                                            <p class="truncate text-xs text-brand-muted">{{ $child->description }}</p>
                                                        @endif
                                                        <p class="text-[10px] text-brand-muted">{{ $child->projects_count }} project{{ $child->projects_count !== 1 ? 's' : '' }}</p>
                                                    </div>
                                                    <div class="flex shrink-0 gap-1.5">
                                                        <button type="button" @click="editing = true"
                                                                class="rounded-lg border border-warm-300/50 p-1.5 text-brand-muted transition hover:border-brand-primary hover:text-brand-primary">
                                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/></svg>
                                                        </button>
                                                        <form method="POST" action="{{ route('admin.categories.destroy', $child) }}"
                                                              onsubmit="return confirm('Delete subcategory \'{{ addslashes($child->name) }}\'?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                    class="rounded-lg border border-warm-300/50 p-1.5 text-brand-muted transition hover:border-red-200 hover:text-red-500">
                                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>

                                                {{-- Inline edit --}}
                                                <div x-show="editing" x-cloak class="w-full">
                                                    <form method="POST" action="{{ route('admin.categories.update', $child) }}"
                                                          enctype="multipart/form-data" class="space-y-3">
                                                        @csrf @method('PATCH')
                                                        <div class="grid gap-2 sm:grid-cols-2">
                                                            <div>
                                                                <label class="block text-[10px] font-semibold text-brand-muted">Name</label>
                                                                <input type="text" name="name" value="{{ $child->name }}" required
                                                                       class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                                                            </div>
                                                            <div>
                                                                <label class="block text-[10px] font-semibold text-brand-muted">Color</label>
                                                                <input type="color" name="color" x-model="color"
                                                                       class="mt-1 h-9 w-full cursor-pointer rounded-xl border border-warm-300/50 p-0.5 shadow-sm">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-semibold text-brand-muted">Description</label>
                                                            <input type="text" name="description" value="{{ $child->description }}"
                                                                   class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-semibold text-brand-muted">Parent</label>
                                                            <select name="parent_id"
                                                                    class="mt-1 w-full rounded-xl border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs text-brand-ink shadow-sm focus:border-brand-primary focus:ring-brand-primary">
                                                                <option value="">— None (top-level) —</option>
                                                                @foreach ($rootCategories as $opt)
                                                                    @if ($opt->id !== $child->id)
                                                                        <option value="{{ $opt->id }}" {{ $child->parent_id == $opt->id ? 'selected' : '' }}>{{ $opt->name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-semibold text-brand-muted">Replace Image</label>
                                                            <input type="file" name="image" accept="image/*"
                                                                   @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                                                                   class="mt-1 w-full rounded-xl border border-warm-300/50 bg-warm-100 px-2 py-1 text-[10px] text-brand-muted shadow-sm">
                                                            <img x-show="preview" :src="preview" alt="New preview"
                                                                 class="mt-1.5 h-14 w-full rounded-xl object-cover" x-cloak>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <button type="submit" class="btn-primary py-1.5 text-[11px]">Save</button>
                                                            <button type="button" @click="editing = false; preview = null" class="btn-secondary py-1.5 text-[11px]">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>{{-- end root card --}}
                    @endforeach
                </div>
            @endif
        </div>{{-- end tree column --}}
    </div>
</x-app-layout>
