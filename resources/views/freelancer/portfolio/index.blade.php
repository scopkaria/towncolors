<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                My Portfolio
            </span>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Portfolio</h1>
                    <p class="max-w-2xl text-sm leading-7 text-brand-muted">
                        Showcase your best work. Items are reviewed by admin before appearing on the public portfolio page.
                    </p>
                </div>
                <a href="{{ route('portfolio.public') }}" target="_blank"
                   class="btn-secondary inline-flex shrink-0 items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                    </svg>
                    View Public Page
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Flash --}}
    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-8 xl:grid-cols-[380px,1fr]">

        {{-- ── Add new item form ── --}}
        <div class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Add Portfolio Item</p>

            <form method="POST"
                  action="{{ route('freelancer.portfolio.store') }}"
                  enctype="multipart/form-data"
                  class="mt-5 space-y-5"
                  x-data="{ preview: null }"
                  @submit="preview = null">
                @csrf

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-semibold text-brand-ink">Title <span class="text-red-400">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                           placeholder="e.g. E-commerce Website Redesign"
                           class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">
                    @error('title')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-brand-ink">Description</label>
                    <textarea id="description" name="description" rows="4"
                              placeholder="Describe the project, your role, technologies used…"
                              class="mt-2 w-full rounded-2xl border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:ring-brand-primary">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Image upload with preview --}}
                <div>
                    <label class="block text-sm font-semibold text-brand-ink">Cover Image</label>

                    <label for="image"
                           class="mt-2 flex cursor-pointer flex-col items-center justify-center rounded-3xl border border-dashed border-orange-200 bg-orange-50/40 px-6 py-8 text-center transition hover:border-brand-primary hover:bg-orange-50"
                           x-show="!preview">
                        <svg class="h-9 w-9 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                        </svg>
                        <span class="mt-3 text-sm font-semibold text-brand-ink">Choose image</span>
                        <span class="mt-1 text-xs text-brand-muted">JPG, PNG, WebP · max 5 MB</span>
                    </label>

                    {{-- Preview --}}
                    <div x-show="preview" x-cloak class="relative mt-2 overflow-hidden rounded-2xl border border-orange-200">
                        <img :src="preview" alt="Preview" class="h-48 w-full object-cover">
                        <button type="button"
                                @click="preview = null; $refs.imageInput.value = ''"
                                class="absolute right-2 top-2 flex h-8 w-8 items-center justify-center rounded-full bg-slate-900/70 text-white transition hover:bg-red-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                        <label for="image" class="absolute bottom-2 right-2">
                            <span class="cursor-pointer rounded-xl bg-white/90 px-3 py-1.5 text-xs font-semibold text-brand-ink shadow transition hover:bg-white">Change</span>
                        </label>
                    </div>

                    <input id="image" name="image" type="file" accept="image/*"
                           x-ref="imageInput"
                           class="sr-only"
                           @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                    @error('image')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full">Submit for Review</button>
            </form>
        </div>

        {{-- ── My items grid ── --}}
        <div>
            <div class="flex items-center justify-between gap-4">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Your Items</p>
                <span class="text-xs text-brand-muted">{{ $items->count() }} total</span>
            </div>

            @if ($items->isEmpty())
                <div class="mt-6 flex flex-col items-center justify-center rounded-3xl border border-dashed border-stone-200 bg-white/60 p-16 text-center">
                    <div class="rounded-2xl bg-orange-50 p-5">
                        <svg class="h-8 w-8 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-display text-xl text-brand-ink">No portfolio items yet</h3>
                    <p class="mt-2 text-sm text-brand-muted">Add your first item using the form on the left.</p>
                </div>
            @else
                <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($items as $item)
                        <div class="group relative flex flex-col overflow-hidden rounded-3xl border border-stone-100 bg-white shadow-card transition duration-300 hover:shadow-panel">

                            {{-- Image --}}
                            <div class="relative h-48 overflow-hidden bg-gradient-to-br from-orange-50 to-amber-50">
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}"
                                         alt="{{ $item->title }}"
                                         class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center">
                                        <svg class="h-12 w-12 text-orange-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Status badge --}}
                                <span class="absolute left-3 top-3 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider backdrop-blur-sm
                                    @if($item->status === 'approved') bg-emerald-500/90 text-white
                                    @elseif($item->status === 'rejected') bg-red-500/90 text-white
                                    @else bg-amber-400/90 text-white @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>

                            {{-- Content --}}
                            <div class="flex flex-1 flex-col p-4">
                                <h3 class="font-display text-base font-semibold text-brand-ink line-clamp-2">{{ $item->title }}</h3>
                                @if ($item->description)
                                    <p class="mt-1.5 flex-1 text-xs leading-relaxed text-brand-muted line-clamp-3">{{ $item->description }}</p>
                                @endif
                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <span class="text-[10px] text-brand-muted">{{ $item->created_at->format('M d, Y') }}</span>
                                    <form method="POST" action="{{ route('freelancer.portfolio.destroy', $item) }}"
                                          onsubmit="return confirm('Delete this portfolio item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-xl border border-stone-200 px-3 py-1.5 text-xs font-medium text-brand-muted transition hover:border-red-200 hover:bg-red-50 hover:text-red-600">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
