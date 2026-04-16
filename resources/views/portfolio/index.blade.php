<x-public-layout title="Portfolio">

@php $settings = \App\Models\Setting::first(); @endphp

@push('head')
<style>
    /* Subtle stagger offset for middle column on large screens */
    @media (min-width: 1024px) {
        .portfolio-card:nth-child(3n+2) { margin-top: 1.5rem; }
    }
</style>
@endpush

<x-public-hero
    badge="Our Work"
    title="Portfolio"
    :subtitle="$settings?->heroSubtitle('portfolio') ?: 'A curated showcase of our delivered projects crafted with precision and care.'"
    :media="$settings?->heroMediaUrl('portfolio')"
/>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  FILTER + GRID                                                  ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="py-12 sm:py-16 lg:py-20"
         x-data="portfolioFilter()"
         x-init="init()">

    <div class="mx-auto max-w-7xl px-4 sm:px-8">

        @php
            $productCount = $items->where('item_type', 'product')->count();
            $projectCount = $items->where('item_type', 'project')->count();
        @endphp

        <div class="reveal mb-6 flex flex-wrap items-center gap-2 rounded-2xl border border-warm-300/50 bg-warm-100/80 p-2 dark:border-slate-700/50 dark:bg-navy-800/80">
            <button
                @click="activeType = 'products'"
                :class="activeType === 'products'
                    ? 'border-emerald-500 bg-emerald-500 text-white shadow-[0_0_0_4px_rgba(16,185,129,0.18)]'
                    : 'border-warm-300/50 bg-warm-100 text-brand-muted hover:border-emerald-200 hover:text-emerald-700 dark:border-slate-700/50 dark:bg-navy-800'"
                class="rounded-xl border px-4 py-2 text-xs font-semibold uppercase tracking-[0.12em] transition duration-200">
                Products ({{ $productCount }})
            </button>
            <button
                @click="activeType = 'projects'"
                :class="activeType === 'projects'
                    ? 'border-brand-primary bg-brand-primary text-white shadow-[0_0_0_4px_rgba(249,115,22,0.14)]'
                    : 'border-warm-300/50 bg-warm-100 text-brand-muted hover:border-accent hover:text-brand-primary'"
                class="rounded-xl border px-4 py-2 text-xs font-semibold uppercase tracking-[0.12em] transition duration-200">
                Projects ({{ $projectCount }})
            </button>
            <button
                @click="activeType = 'all'"
                :class="activeType === 'all'
                    ? 'border-slate-700 bg-slate-700 text-white'
                    : 'border-warm-300/50 bg-warm-100 text-brand-muted hover:border-warm-400 hover:text-brand-ink'"
                class="rounded-xl border px-4 py-2 text-xs font-semibold uppercase tracking-[0.12em] transition duration-200">
                All Listings
            </button>
            <p class="ml-auto text-xs text-brand-muted" x-show="activeType === 'products'" x-cloak>
                Ready-to-buy business apps with pricing and instant inquiry links.
            </p>
        </div>

        {{-- ── Filter bar ── --}}
        <div class="reveal mb-8 flex flex-col gap-4 sm:mb-10 sm:flex-row sm:items-center sm:gap-6">

            {{-- Search --}}
            <div class="relative flex-1 max-w-xs">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input
                    type="text"
                    x-model="query"
                    placeholder="Search projects or products..."
                    class="w-full rounded-2xl border border-warm-300/50 bg-warm-100 py-2.5 pl-10 pr-9 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:outline-none focus:ring-1 focus:ring-brand-primary dark:border-warm-400/[0.10] dark:bg-navy-800">
                <button x-show="query" x-cloak @click="query = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-0.5 text-brand-muted/50 transition hover:text-brand-ink">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Freelancer filter chips --}}
            @if ($freelancers->count() > 1)
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="freelancer = ''"
                        :class="freelancer === ''
                            ? 'border-brand-primary bg-brand-primary text-white shadow-[0_0_0_4px_rgba(249,115,22,0.12)]'
                            : 'border-warm-300/50 bg-warm-100 text-brand-muted hover:border-accent hover:text-brand-primary dark:border-warm-400/[0.12] dark:bg-navy-800 dark:hover:border-accent'"
                        class="rounded-xl border px-3.5 py-1.5 text-xs font-semibold transition duration-200">
                        All
                    </button>
                    @foreach ($freelancers as $fl)
                        <button
                            @click="freelancer = @js($fl->name)"
                            :class="freelancer === @js($fl->name)
                                ? 'border-brand-primary bg-brand-primary text-white shadow-[0_0_0_4px_rgba(249,115,22,0.12)]'
                                : 'border-warm-300/50 bg-warm-100 text-brand-muted hover:border-accent hover:text-brand-primary dark:border-slate-700/50 dark:bg-navy-800'"
                            class="rounded-xl border px-3.5 py-1.5 text-xs font-semibold transition duration-200">
                            {{ $fl->name }}
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Result count --}}
            <p class="shrink-0 text-sm text-brand-muted sm:ml-auto">
                <span class="font-semibold text-brand-ink" x-text="count"></span>
                <span x-text="parseInt(count) === 1 ? ' item' : ' items'"></span>
            </p>
        </div>

        @if (!empty($categories) && $categories->isNotEmpty())
            <div class="reveal mb-8 rounded-2xl border border-warm-300/50 bg-warm-100/80 p-3 sm:mb-10 dark:border-slate-700/50 dark:bg-navy-800/80">
                <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.14em] text-brand-muted">Category Filter</p>
                <div class="flex flex-wrap gap-2">
                    <button
                        @click="activeCategory = ''"
                        :class="activeCategory === ''
                            ? 'border-slate-700 bg-slate-700 text-white'
                            : 'border-warm-300/50 bg-warm-100 text-brand-muted hover:border-warm-400 hover:text-brand-ink dark:border-slate-700/50 dark:bg-navy-800'"
                        class="rounded-xl border px-3.5 py-1.5 text-xs font-semibold transition duration-200">
                        All Categories
                    </button>
                    @foreach ($categories as $category)
                        <button
                            @click="activeCategory = @js($category)"
                            :class="activeCategory === @js($category)
                                ? 'border-brand-primary bg-brand-primary text-white shadow-[0_0_0_4px_rgba(249,115,22,0.12)]'
                                : 'border-warm-300/50 bg-warm-100 text-brand-muted hover:border-accent hover:text-brand-primary dark:border-slate-700/50 dark:bg-navy-800'"
                            class="rounded-xl border px-3.5 py-1.5 text-xs font-semibold transition duration-200">
                            {{ $category }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($items->isEmpty())
            {{-- Empty state --}}
            <div class="reveal flex flex-col items-center justify-center py-28 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-warm-300/40 bg-warm-100 shadow-card dark:border-slate-700/40 dark:bg-navy-800">
                    <svg class="h-9 w-9 text-brand-muted/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                    </svg>
                </div>
                <h2 class="mt-5 font-display text-xl text-brand-ink">No portfolio items yet</h2>
                <p class="mt-2 max-w-xs text-sm leading-6 text-brand-muted">Check back soon — our freelancers are working on great things.</p>
            </div>
        @else
            {{-- Zero results message --}}
            <div x-show="parseInt(count) === 0" x-cloak class="py-20 text-center">
                <p class="text-base font-semibold text-brand-ink">No results found</p>
                <p class="mt-1 text-sm text-brand-muted">Try adjusting your search or filter.</p>
                <button @click="query = ''; freelancer = ''; activeCategory = ''" class="mt-4 text-sm font-semibold text-brand-primary hover:underline">
                    Clear all filters
                </button>
            </div>

            {{-- Card grid --}}
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $index => $item)
                    <article
                        class="portfolio-card group relative flex flex-col overflow-hidden rounded-2xl border border-warm-300/40 bg-warm-100 shadow-card transition-all duration-300 ease-out dark:border-slate-700/50 dark:bg-navy-800/80 sm:rounded-3xl {{ ($item->item_type ?? 'project') === 'product' ? 'ring-1 ring-emerald-200/80' : '' }}"
                        style="--delay: {{ ($index % 3) * 60 }}ms"
                        x-show="isVisible(
                            @js($item->title),
                            @js($item->freelancer?->name ?? ''),
                            @js(($item->item_type ?? 'project') === 'product' ? 'products' : 'projects'),
                            @js(trim((string) ($item->industry ?? '') . ' ' . implode(' ', (array) ($item->services ?? []))))
                        )"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">

                        {{-- ── Image ── --}}
                        <div class="relative h-52 shrink-0 overflow-hidden bg-gradient-to-br from-accent-light to-amber-50 sm:h-56 dark:from-navy-800 dark:to-navy-800">
                            @if ($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                     alt="{{ $item->title }}"
                                     loading="lazy"
                                     class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.07]">
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <svg class="h-14 w-14 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                                    </svg>
                                </div>
                            @endif

{{-- Hover tone --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-stone-900/30 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>

                        {{-- Freelancer badge (slides up) --}}
                        @if ($item->freelancer)
                            <div class="absolute bottom-0 left-0 right-0 translate-y-full px-4 py-3.5 transition-transform duration-300 ease-out group-hover:translate-y-0">
                                <div class="flex items-center gap-2.5">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/90 font-display text-sm font-bold text-brand-primary shadow ring-1 ring-warm-300/50">
                                        {{ strtoupper(substr($item->freelancer->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold text-white drop-shadow">{{ $item->freelancer->name }}</p>
                                        <p class="text-[10px] text-white/80">Creator</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Date / featured badges --}}
                            <div class="absolute right-3 top-3 flex items-center gap-2">
                                @if ($item->featured)
                                    <span class="rounded-xl border border-amber-200/70 bg-amber-100/90 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-amber-800 backdrop-blur-sm">
                                        Featured
                                    </span>
                                @endif
                                <span class="rounded-xl border border-white/10 bg-black/30 px-2.5 py-1 text-[10px] font-medium text-white/80 backdrop-blur-sm">
                                    {{ $item->completion_year ?: $item->created_at->format('Y') }}
                                </span>
                            </div>

                            {{-- Orange accent line --}}
                            <div class="absolute bottom-0 left-0 right-0 h-0.5 origin-left scale-x-0 bg-brand-primary transition-transform duration-300 group-hover:scale-x-100"></div>
                        </div>

                        {{-- ── Content ── --}}
                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                            <h2 class="font-display text-[1rem] leading-snug text-brand-ink transition-colors duration-200 group-hover:text-brand-primary line-clamp-2 sm:text-[1.0625rem]">
                                {{ $item->title }}
                            </h2>

                            <div class="mt-2 flex items-center gap-2">
                                <span class="rounded-full border px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] {{ ($item->item_type ?? 'project') === 'product' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-accent/30 bg-accent-light text-brand-primary' }}">
                                    {{ ($item->item_type ?? 'project') === 'product' ? 'Product' : 'Project' }}
                                </span>
                                @if ($item->is_purchasable && $item->price)
                                    <span class="rounded-full border border-brand-primary/20 bg-brand-primary/10 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-brand-primary">
                                        {{ $item->currency ?? 'USD' }} {{ number_format((float) $item->price, 2) }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-brand-primary/90">
                                @if ($item->client_name)
                                    <span>{{ $item->client_name }}</span>
                                @endif
                                @if ($item->industry)
                                    <span class="inline-flex h-1 w-1 rounded-full bg-warm-400"></span>
                                    <span>{{ $item->industry }}</span>
                                @endif
                                @if ($item->country)
                                    <span class="inline-flex h-1 w-1 rounded-full bg-warm-400"></span>
                                    <span>{{ $item->country }}</span>
                                @endif
                            </div>

                            @if ($item->description)
                                <p class="mt-2 flex-1 text-sm leading-6 text-brand-muted line-clamp-3">
                                    {{ $item->description }}
                                </p>
                            @else
                                <div class="flex-1"></div>
                            @endif

                            @if (!empty($item->services))
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach (array_slice($item->services, 0, 3) as $service)
                                        <span class="rounded-full border border-warm-300/50 bg-warm-200/50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-brand-muted dark:border-slate-700/50 dark:bg-navy-700/50">{{ $service }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if (!empty($item->technologies))
                                <p class="mt-3 text-xs text-brand-muted line-clamp-1">
                                    <span class="font-semibold text-brand-ink">Tech:</span>
                                    {{ implode(', ', $item->technologies) }}
                                </p>
                            @endif

                            @if ($item->results)
                                <p class="mt-2 text-xs leading-6 text-brand-muted line-clamp-2">
                                    <span class="font-semibold text-brand-ink">Outcome:</span>
                                    {{ $item->results }}
                                </p>
                            @endif

                            @if ($item->project_url)
                                <a href="{{ $item->project_url }}" target="_blank" rel="noopener"
                                   class="mt-4 inline-flex w-fit items-center gap-2 rounded-xl border border-accent/30 bg-accent-light px-3 py-1.5 text-xs font-semibold text-brand-primary transition hover:bg-brand-primary hover:text-white">
                                    Visit Website
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                </a>
                            @endif

                            @if (($item->item_type ?? 'project') === 'product')
                                <a href="{{ route('shop.show', $item) }}"
                                   class="mt-2 inline-flex w-fit items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50/60 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-600 hover:text-white">
                                    View Product
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                </a>
                            @endif

                            @if ($item->is_purchasable && $item->purchase_url)
                                <a href="{{ $item->purchase_url }}" target="_blank" rel="noopener"
                                   class="mt-2 inline-flex w-fit items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50/60 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-600 hover:text-white">
                                    Buy Now
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h1.664a1 1 0 0 1 .948.684l.623 1.871M7 13h10l3-8H6.235m.765 8L5.106 5.316M7 13l-1.293 5.172A1 1 0 0 0 6.677 19h10.646a1 1 0 0 0 .97-.757L19 14M9 21a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm8 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/></svg>
                                </a>
                            @endif

                            {{-- Footer --}}
                            <div class="mt-4 flex items-center justify-between gap-2 border-t border-warm-300/40 pt-4 dark:border-slate-700/40">
                                @if ($item->freelancer)
                                    <div class="flex min-w-0 items-center gap-2">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-accent-light font-display text-xs font-bold text-brand-primary">
                                            {{ strtoupper(substr($item->freelancer->name, 0, 1)) }}
                                        </span>
                                        <span class="truncate text-xs font-medium text-brand-muted">
                                            {{ $item->freelancer->name }}
                                        </span>
                                    </div>
                                @else
                                    <div></div>
                                @endif

                                <span class="flex shrink-0 items-center gap-1 text-xs font-semibold text-brand-primary opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                    View
                                    <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  CTA STRIP                                                      ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="border-t border-warm-300/40 bg-warm-100/60 py-12 sm:py-16 dark:border-slate-700/40 dark:bg-navy-900/60">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        <div class="reveal flex flex-col items-center gap-5 text-center sm:flex-row sm:justify-between sm:text-left">
            <div>
                <h2 class="font-display text-xl text-brand-ink sm:text-2xl">Like what you see?</h2>
                <p class="mt-1.5 text-sm text-brand-muted sm:mt-2 sm:text-base">
                    Start your project today and join our growing list of happy clients.
                </p>
            </div>
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                <a href="{{ url('/client/projects/create') }}" class="btn-primary">
                    <svg class="mr-2 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Start a Project
                </a>
                <a href="{{ route('services.index') }}" class="btn-secondary">Browse Services</a>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function portfolioFilter() {
    return {
        query:     '',
        freelancer: '',
        activeCategory: '',
        activeType: {{ $productCount > 0 ? "'products'" : "'all'" }},
        count:     {{ $items->count() }},

        init() {
            this.updateCount();
            this.$watch('query',      () => this.updateCount());
            this.$watch('freelancer', () => this.updateCount());
            this.$watch('activeCategory', () => this.updateCount());
            this.$watch('activeType', () => this.updateCount());
        },

        isVisible(title, author, type, categoriesText) {
            const q = this.query.trim().toLowerCase();
            const categoryBlob = (categoriesText || '').toLowerCase();
            const matchQuery      = !q || title.toLowerCase().includes(q) || author.toLowerCase().includes(q) || categoryBlob.includes(q);
            const matchFreelancer = !this.freelancer || author === this.freelancer;
            const matchType = this.activeType === 'all' || this.activeType === type;
            const matchCategory = !this.activeCategory || categoryBlob.includes(this.activeCategory.toLowerCase());
            return matchQuery && matchFreelancer && matchType && matchCategory;
        },

        updateCount() {
            this.$nextTick(() => {
                this.count = this.$el.querySelectorAll('.portfolio-card:not([style*="display: none"])').length;
            });
        },
    };
}
</script>
@endpush

</x-public-layout>
