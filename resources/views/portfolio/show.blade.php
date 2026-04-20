<x-public-layout :title="$portfolio->title . ' · Portfolio'">

@php
    $gallery = collect($portfolio->product_gallery ?? [])->filter()->values();
    if ($portfolio->image_path) {
        $gallery = $gallery->prepend($portfolio->image_path)->unique()->values();
    }
@endphp

@push('head')
<meta name="description" content="{{ Str::limit($portfolio->description, 160) }}">
<link rel="canonical" href="{{ route('portfolio.show', $portfolio) }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $portfolio->title }} - {{ config('app.name') }}">
<meta property="og:description" content="{{ Str::limit($portfolio->description, 160) }}">
@if ($portfolio->image_path)
    <meta property="og:image" content="{{ asset('storage/' . $portfolio->image_path) }}">
@endif
<meta property="og:url" content="{{ route('portfolio.show', $portfolio) }}">
@endpush

<x-public-hero
    badge="Portfolio"
    :title="$portfolio->title"
    :subtitle="Str::limit($portfolio->description, 200)"
    :media="$portfolio->image_path ? asset('storage/' . $portfolio->image_path) : null"
    heightClass="min-h-[520px] sm:min-h-[620px]"
/>

{{-- Quick stats bar --}}
<section class="border-b border-warm-300/40 bg-warm-100 py-8 dark:border-slate-700/40 dark:bg-navy-900">
    <div class="mx-auto grid max-w-7xl gap-4 px-4 sm:grid-cols-4 sm:px-8">
        <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Category</p>
            <p class="mt-1 text-sm font-semibold text-brand-ink">{{ $portfolio->industry ?: 'Digital Solution' }}</p>
        </div>
        @if ($portfolio->completion_year)
        <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Year</p>
            <p class="mt-1 text-sm font-semibold text-brand-ink">{{ $portfolio->completion_year }}</p>
        </div>
        @endif
        @if ($portfolio->duration)
        <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Duration</p>
            <p class="mt-1 text-sm font-semibold text-brand-ink">{{ $portfolio->duration }}</p>
        </div>
        @endif
        @if ($portfolio->country)
        <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Location</p>
            <p class="mt-1 text-sm font-semibold text-brand-ink">{{ $portfolio->country }}</p>
        </div>
        @endif
    </div>
</section>

{{-- Main content --}}
<section class="py-14 sm:py-20">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-8 lg:grid-cols-[1.1fr_0.9fr]">

        {{-- Left: Overview + Gallery --}}
        <div>
            <article class="rounded-3xl border border-warm-300/50 bg-warm-100 p-6 shadow-sm sm:p-8 dark:border-slate-700/50 dark:bg-navy-800">
                <h2 class="font-display text-2xl text-brand-ink">Project Overview</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-8 text-brand-muted sm:text-base">{{ $portfolio->description }}</p>

                @if ($portfolio->results)
                    <div class="mt-7 rounded-2xl border border-emerald-200/60 bg-emerald-50/60 p-4 dark:border-emerald-700/30 dark:bg-emerald-900/20">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700 dark:text-emerald-400">Results & Impact</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-brand-muted">{{ $portfolio->results }}</p>
                    </div>
                @endif

                @if ($portfolio->extra_info)
                    <div class="mt-5 rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-primary">Additional Information</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-brand-muted">{{ $portfolio->extra_info }}</p>
                    </div>
                @endif
            </article>

            {{-- Gallery --}}
            @if ($gallery->count() > 1)
                <div class="mt-8">
                    <h3 class="mb-4 font-display text-xl text-brand-ink">Gallery</h3>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach ($gallery as $imagePath)
                            <button type="button"
                                class="group overflow-hidden rounded-2xl border border-warm-300/50 bg-warm-100 shadow-sm dark:border-slate-700/50 dark:bg-navy-800"
                                x-data="{}" @click="$dispatch('open-gallery-image', { src: '{{ asset('storage/' . $imagePath) }}' })">
                                <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $portfolio->title }}" class="h-56 w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: Details sidebar --}}
        <aside>
            <div class="rounded-3xl border border-warm-300/50 bg-warm-100 p-6 shadow-sm sm:p-8 dark:border-slate-700/50 dark:bg-navy-800">

                {{-- Services / Features --}}
                @if (!empty($portfolio->services))
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Features & Services</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($portfolio->services as $service)
                                <span class="rounded-full border border-warm-300/50 bg-warm-200/50 px-3 py-1 text-[11px] font-semibold text-brand-muted dark:border-slate-700/50 dark:bg-navy-700/50">{{ $service }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Technologies --}}
                @if (!empty($portfolio->technologies))
                    <div class="mt-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Technologies Used</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($portfolio->technologies as $tech)
                                <span class="rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold text-brand-primary dark:border-accent/20 dark:bg-accent/10">{{ $tech }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Brand credit --}}
                <div class="mt-8 rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-brand-primary">Built By</p>
                    <p class="mt-1 font-display text-lg text-brand-ink">Town Colors</p>
                    <p class="mt-1 text-xs text-brand-muted">Professional digital solutions crafted in Arusha, Tanzania.</p>
                </div>

                {{-- CTA --}}
                <div class="mt-6 flex flex-col gap-3">
                    @if ($portfolio->project_url)
                        <a href="{{ $portfolio->project_url }}" target="_blank" rel="noopener" class="btn-primary w-full justify-center">
                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                            Visit Live Project
                        </a>
                    @endif
                    @if ($portfolio->is_purchasable && $portfolio->item_type === 'product')
                        <a href="{{ route('shop.show', $portfolio) }}" class="btn-secondary w-full justify-center">View in Shop</a>
                    @endif
                    <a href="{{ route('contact.show') }}" class="btn-secondary w-full justify-center">Start a Similar Project</a>
                </div>
            </div>
        </aside>
    </div>
</section>

{{-- Related work --}}
@if ($relatedItems->isNotEmpty())
<section class="border-t border-warm-300/40 bg-warm-200/50 py-14 sm:py-20 dark:border-slate-700/40 dark:bg-navy-900/60">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        <h2 class="font-display text-3xl text-brand-ink">More Work</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($relatedItems as $related)
                <a href="{{ route('portfolio.show', $related->slug) }}" class="group overflow-hidden rounded-2xl border border-warm-300/50 bg-warm-100 shadow-sm transition hover:-translate-y-0.5 hover:shadow-card dark:border-slate-700/50 dark:bg-navy-800">
                    @if ($related->image_path)
                        <img src="{{ asset('storage/' . $related->image_path) }}" alt="{{ $related->title }}" class="h-44 w-full object-cover transition duration-300 group-hover:scale-105" loading="lazy">
                    @else
                        <div class="flex h-44 items-center justify-center bg-gradient-to-br from-accent-light to-warm-200">
                            <svg class="h-10 w-10 text-brand-primary/20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <div class="p-4 sm:p-5">
                        <h3 class="font-display text-base text-brand-ink transition group-hover:text-brand-primary sm:text-lg">{{ $related->title }}</h3>
                        <p class="mt-1.5 text-sm text-brand-muted line-clamp-2">{{ $related->description }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Lightbox --}}
<div x-data="{ open:false, src:'' }" @open-gallery-image.window="open=true;src=$event.detail.src" x-show="open" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center bg-navy-900/75 p-4" @click.self="open=false" @keydown.escape.window="open=false">
    <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white" @click="open=false">×</button>
    <img :src="src" alt="Preview" class="max-h-[90vh] max-w-[90vw] rounded-xl object-contain shadow-2xl">
</div>

</x-public-layout>
