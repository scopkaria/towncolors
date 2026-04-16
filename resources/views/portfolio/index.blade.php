<x-public-layout title="Portfolio">

@php $settings = \App\Models\Setting::first(); @endphp

<x-public-hero
    badge="Our Work"
    title="Portfolio"
    :subtitle="$settings?->heroSubtitle('portfolio') ?: 'A curated showcase of projects and products crafted with precision, strategy, and care.'"
    :media="$settings?->heroMediaUrl('portfolio')"
/>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  STATS STRIP                                                     ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
@php
    $productCount = $items->where('item_type', 'product')->count();
    $projectCount = $items->where('item_type', 'project')->count();
    $totalCount   = $items->count();
    $countryCount = $items->pluck('country')->filter()->unique()->count();
@endphp

<section class="border-b border-warm-300/40 bg-warm-100/60 py-8 dark:border-slate-700/40 dark:bg-navy-900/60">
    <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-center gap-8 px-4 text-center sm:gap-12 sm:px-8 lg:gap-20">
        <div>
            <p class="font-display text-3xl text-brand-ink sm:text-4xl">{{ $totalCount }}</p>
            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-brand-muted">Completed Works</p>
        </div>
        <div class="hidden h-8 w-px bg-warm-300/50 sm:block dark:bg-slate-700/50"></div>
        <div>
            <p class="font-display text-3xl text-brand-ink sm:text-4xl">{{ $projectCount }}</p>
            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-brand-muted">Client Projects</p>
        </div>
        <div class="hidden h-8 w-px bg-warm-300/50 sm:block dark:bg-slate-700/50"></div>
        <div>
            <p class="font-display text-3xl text-brand-ink sm:text-4xl">{{ $productCount }}</p>
            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-brand-muted">Digital Products</p>
        </div>
        @if ($countryCount > 1)
        <div class="hidden h-8 w-px bg-warm-300/50 sm:block dark:bg-slate-700/50"></div>
        <div>
            <p class="font-display text-3xl text-brand-ink sm:text-4xl">{{ $countryCount }}</p>
            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.14em] text-brand-muted">Countries Served</p>
        </div>
        @endif
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  FILTER + GRID                                                  ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="py-12 sm:py-16 lg:py-20"
         x-data="portfolioFilter()"
         x-init="init()">

    <div class="mx-auto max-w-7xl px-4 sm:px-8">

        {{-- ── Filter bar ── --}}
        <div class="reveal mb-8 flex flex-col gap-4 rounded-2xl border border-warm-300/50 bg-warm-100/80 p-4 sm:mb-10 sm:flex-row sm:items-center sm:gap-4 dark:border-slate-700/50 dark:bg-navy-800/80">

            {{-- Type tabs --}}
            <div class="flex gap-1.5">
                <button @click="activeType = 'all'"
                    :class="activeType === 'all'
                        ? 'bg-brand-primary text-white shadow-sm'
                        : 'text-brand-muted hover:text-brand-ink dark:hover:text-warm-200'"
                    class="rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-[0.1em] transition duration-200">
                    All ({{ $totalCount }})
                </button>
                <button @click="activeType = 'projects'"
                    :class="activeType === 'projects'
                        ? 'bg-brand-primary text-white shadow-sm'
                        : 'text-brand-muted hover:text-brand-ink dark:hover:text-warm-200'"
                    class="rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-[0.1em] transition duration-200">
                    Projects ({{ $projectCount }})
                </button>
                @if ($productCount > 0)
                <button @click="activeType = 'products'"
                    :class="activeType === 'products'
                        ? 'bg-emerald-500 text-white shadow-sm'
                        : 'text-brand-muted hover:text-brand-ink dark:hover:text-warm-200'"
                    class="rounded-xl px-3.5 py-2 text-xs font-semibold uppercase tracking-[0.1em] transition duration-200">
                    Products ({{ $productCount }})
                </button>
                @endif
            </div>

            <div class="hidden h-6 w-px bg-warm-300/50 sm:block dark:bg-slate-700/50"></div>

            {{-- Search --}}
            <div class="relative flex-1 max-w-sm">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text" x-model="query" placeholder="Search portfolio..."
                    class="w-full rounded-xl border border-warm-300/50 bg-white py-2 pl-10 pr-9 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:outline-none focus:ring-1 focus:ring-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">
                <button x-show="query" x-cloak @click="query = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-muted/50 hover:text-brand-ink">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Category dropdown --}}
            @if ($categories->isNotEmpty())
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                    class="flex items-center gap-2 rounded-xl border border-warm-300/50 bg-white px-3.5 py-2 text-sm text-brand-ink shadow-sm transition hover:border-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">
                    <svg class="h-4 w-4 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z"/></svg>
                    <span x-text="activeCategory || 'All Categories'" class="max-w-[140px] truncate"></span>
                    <svg class="h-3.5 w-3.5 shrink-0 text-brand-muted transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition
                     class="absolute left-0 top-full z-30 mt-2 max-h-64 w-64 overflow-y-auto rounded-2xl border border-warm-300/50 bg-white p-2 shadow-xl dark:border-slate-700/50 dark:bg-navy-800">
                    <button @click="activeCategory = ''; open = false"
                        :class="activeCategory === '' ? 'bg-brand-primary/10 text-brand-primary' : 'text-brand-muted hover:bg-warm-200/50 dark:hover:bg-navy-700/50'"
                        class="w-full rounded-xl px-3 py-2 text-left text-sm font-medium transition">
                        All Categories
                    </button>
                    @foreach ($categories as $category)
                    <button @click="activeCategory = @js($category); open = false"
                        :class="activeCategory === @js($category) ? 'bg-brand-primary/10 text-brand-primary' : 'text-brand-ink hover:bg-warm-200/50 dark:text-warm-200 dark:hover:bg-navy-700/50'"
                        class="w-full rounded-xl px-3 py-2 text-left text-sm transition">
                        {{ $category }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Freelancer filter --}}
            @if ($freelancers->count() > 1)
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                    class="flex items-center gap-2 rounded-xl border border-warm-300/50 bg-white px-3.5 py-2 text-sm text-brand-ink shadow-sm transition hover:border-brand-primary dark:border-slate-600 dark:bg-navy-900/60 dark:text-warm-100">
                    <svg class="h-4 w-4 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                    <span x-text="freelancer || 'All Creators'" class="max-w-[120px] truncate"></span>
                    <svg class="h-3.5 w-3.5 shrink-0 text-brand-muted transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="open" x-cloak x-transition
                     class="absolute left-0 top-full z-30 mt-2 max-h-64 w-56 overflow-y-auto rounded-2xl border border-warm-300/50 bg-white p-2 shadow-xl dark:border-slate-700/50 dark:bg-navy-800">
                    <button @click="freelancer = ''; open = false"
                        :class="freelancer === '' ? 'bg-brand-primary/10 text-brand-primary' : 'text-brand-muted hover:bg-warm-200/50 dark:hover:bg-navy-700/50'"
                        class="w-full rounded-xl px-3 py-2 text-left text-sm font-medium transition">
                        All Creators
                    </button>
                    @foreach ($freelancers as $fl)
                    <button @click="freelancer = @js($fl->name); open = false"
                        :class="freelancer === @js($fl->name) ? 'bg-brand-primary/10 text-brand-primary' : 'text-brand-ink hover:bg-warm-200/50 dark:text-warm-200 dark:hover:bg-navy-700/50'"
                        class="w-full rounded-xl px-3 py-2 text-left text-sm transition">
                        {{ $fl->name }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Result count --}}
            <p class="text-sm text-brand-muted sm:ml-auto">
                <span class="font-semibold text-brand-ink" x-text="count"></span>
                <span x-text="parseInt(count) === 1 ? ' result' : ' results'"></span>
            </p>
        </div>

        {{-- Active filter pills --}}
        <div class="reveal mb-6 flex flex-wrap gap-2" x-show="activeCategory || freelancer || query" x-cloak>
            <template x-if="activeCategory">
                <button @click="activeCategory = ''" class="inline-flex items-center gap-1.5 rounded-full border border-brand-primary/30 bg-brand-primary/10 px-3 py-1 text-xs font-semibold text-brand-primary transition hover:bg-brand-primary/20">
                    <span x-text="activeCategory"></span>
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </template>
            <template x-if="freelancer">
                <button @click="freelancer = ''" class="inline-flex items-center gap-1.5 rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-xs font-semibold text-brand-primary transition hover:bg-accent/20">
                    <span x-text="freelancer"></span>
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </template>
            <template x-if="query">
                <button @click="query = ''" class="inline-flex items-center gap-1.5 rounded-full border border-warm-300/50 bg-warm-200/50 px-3 py-1 text-xs font-semibold text-brand-muted transition hover:bg-warm-300/50 dark:border-slate-700/50 dark:bg-navy-700/50">
                    "<span x-text="query"></span>"
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </template>
            <button @click="query = ''; freelancer = ''; activeCategory = ''" class="text-xs font-semibold text-brand-muted hover:text-brand-primary transition">
                Clear all
            </button>
        </div>

        @if ($items->isEmpty())
            {{-- Empty state --}}
            <div class="reveal flex flex-col items-center justify-center py-28 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-warm-300/40 bg-warm-100 shadow-card dark:border-slate-700/40 dark:bg-navy-800">
                    <svg class="h-9 w-9 text-brand-muted/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                    </svg>
                </div>
                <h2 class="mt-5 font-display text-xl text-brand-ink">No portfolio items yet</h2>
                <p class="mt-2 max-w-xs text-sm leading-6 text-brand-muted">Check back soon — our team is working on great things.</p>
            </div>
        @else
            {{-- Zero results message --}}
            <div x-show="parseInt(count) === 0" x-cloak class="py-20 text-center">
                <svg class="mx-auto h-12 w-12 text-brand-muted/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <p class="mt-4 text-base font-semibold text-brand-ink">No results found</p>
                <p class="mt-1 text-sm text-brand-muted">Try adjusting your search or filters.</p>
                <button @click="query = ''; freelancer = ''; activeCategory = ''; activeType = 'all'" class="mt-4 text-sm font-semibold text-brand-primary hover:underline">
                    Clear all filters
                </button>
            </div>

            {{-- Card grid --}}
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $index => $item)
                    <article
                        class="portfolio-card group relative flex flex-col overflow-hidden rounded-3xl border border-warm-300/40 bg-white shadow-card transition-all duration-300 hover:shadow-lg hover:-translate-y-1 dark:border-slate-700/50 dark:bg-navy-800/80"
                        x-show="isVisible(
                            @js($item->title),
                            @js($item->freelancer?->name ?? ''),
                            @js(($item->item_type ?? 'project') === 'product' ? 'products' : 'projects'),
                            @js(trim((string) ($item->industry ?? '') . ' ' . implode(' ', (array) ($item->services ?? []))))
                        )"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">

                        {{-- ── Image ── --}}
                        <div class="relative h-56 shrink-0 overflow-hidden bg-gradient-to-br from-warm-200 to-warm-100 dark:from-navy-700 dark:to-navy-800">
                            @if ($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                     alt="{{ $item->title }}"
                                     loading="lazy"
                                     class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <svg class="h-16 w-16 text-warm-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Overlay gradient --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>

                            {{-- Top badges --}}
                            <div class="absolute left-3 top-3 flex items-center gap-2">
                                <span class="rounded-lg px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.1em] backdrop-blur-md {{ ($item->item_type ?? 'project') === 'product' ? 'border border-emerald-300/30 bg-emerald-500/90 text-white' : 'border border-white/20 bg-black/40 text-white' }}">
                                    {{ ($item->item_type ?? 'project') === 'product' ? 'Product' : 'Project' }}
                                </span>
                                @if ($item->featured)
                                    <span class="rounded-lg border border-amber-300/50 bg-amber-400/90 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.1em] text-amber-900 backdrop-blur-sm">
                                        ★ Featured
                                    </span>
                                @endif
                            </div>

                            {{-- Year badge --}}
                            <div class="absolute right-3 top-3">
                                <span class="rounded-lg border border-white/10 bg-black/30 px-2 py-1 text-[10px] font-medium text-white/80 backdrop-blur-sm">
                                    {{ $item->completion_year ?: $item->created_at->format('Y') }}
                                </span>
                            </div>

                            {{-- Price tag for products --}}
                            @if ($item->is_purchasable && $item->price)
                                <div class="absolute bottom-3 right-3">
                                    <span class="rounded-xl border border-emerald-300/50 bg-emerald-500 px-3 py-1.5 text-xs font-bold text-white shadow-lg backdrop-blur-sm">
                                        {{ $item->currency ?? 'USD' }} {{ number_format((float) $item->price, 0) }}
                                    </span>
                                </div>
                            @endif

                            {{-- Orange accent line --}}
                            <div class="absolute bottom-0 left-0 right-0 h-0.5 origin-left scale-x-0 bg-brand-primary transition-transform duration-300 group-hover:scale-x-100"></div>
                        </div>

                        {{-- ── Content ── --}}
                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                            <h2 class="font-display text-base leading-snug text-brand-ink transition-colors group-hover:text-brand-primary line-clamp-2 sm:text-[1.0625rem]">
                                {{ $item->title }}
                            </h2>

                            {{-- Meta row --}}
                            <div class="mt-2.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-brand-muted">
                                @if ($item->industry)
                                    <span class="flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                                        {{ $item->industry }}
                                    </span>
                                @endif
                                @if ($item->country)
                                    <span class="flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                        {{ $item->country }}
                                    </span>
                                @endif
                                @if ($item->client_name)
                                    <span class="flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0"/></svg>
                                        {{ $item->client_name }}
                                    </span>
                                @endif
                            </div>

                            @if ($item->description)
                                <p class="mt-3 flex-1 text-sm leading-relaxed text-brand-muted line-clamp-3">
                                    {{ $item->description }}
                                </p>
                            @else
                                <div class="flex-1"></div>
                            @endif

                            {{-- Tags --}}
                            @if (!empty($item->services))
                                <div class="mt-3.5 flex flex-wrap gap-1.5">
                                    @foreach (array_slice($item->services, 0, 3) as $service)
                                        <span class="rounded-lg border border-warm-300/40 bg-warm-100 px-2 py-0.5 text-[10px] font-semibold text-brand-muted dark:border-slate-700/50 dark:bg-navy-700/50 dark:text-warm-300">{{ $service }}</span>
                                    @endforeach
                                    @if (count($item->services) > 3)
                                        <span class="rounded-lg border border-warm-300/40 bg-warm-100 px-2 py-0.5 text-[10px] font-semibold text-brand-muted dark:border-slate-700/50 dark:bg-navy-700/50">+{{ count($item->services) - 3 }}</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Footer --}}
                            <div class="mt-4 flex items-center justify-between border-t border-warm-300/40 pt-4 dark:border-slate-700/40">
                                @if ($item->freelancer)
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-primary to-accent font-display text-xs font-bold text-white">
                                            {{ strtoupper(substr($item->freelancer->name, 0, 1)) }}
                                        </span>
                                        <span class="text-xs font-medium text-brand-muted">{{ $item->freelancer->name }}</span>
                                    </div>
                                @else
                                    <div></div>
                                @endif

                                <div class="flex items-center gap-2">
                                    @if ($item->project_url)
                                        <a href="{{ $item->project_url }}" target="_blank" rel="noopener"
                                           class="rounded-lg border border-warm-300/50 p-1.5 text-brand-muted transition hover:border-brand-primary hover:text-brand-primary dark:border-slate-700/50"
                                           title="Visit live site">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                                        </a>
                                    @endif
                                    @if (($item->item_type ?? 'project') === 'product')
                                        <a href="{{ route('shop.show', $item) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                                            View
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-primary opacity-0 transition group-hover:opacity-100">
                                            Details
                                            <svg class="h-3 w-3 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  HOW WE WORK                                                    ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="border-t border-warm-300/40 bg-warm-100/60 py-14 sm:py-20 dark:border-slate-700/40 dark:bg-navy-900/60">
    <div class="mx-auto max-w-6xl px-4 sm:px-8">
        <div class="reveal text-center">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-4 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-primary">Our Process</span>
            <h2 class="mt-4 font-display text-2xl text-brand-ink sm:text-3xl">How Every Project Comes to Life</h2>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-brand-muted sm:text-base">
                From the first conversation to the final launch, we follow a proven process that turns ideas into polished digital solutions.
            </p>
        </div>

        <div class="reveal mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="relative rounded-2xl border border-warm-300/40 bg-white p-6 text-center shadow-card dark:border-slate-700/50 dark:bg-navy-800/80">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-primary text-lg font-bold text-white shadow-sm">1</div>
                <h3 class="mt-4 font-display text-sm text-brand-ink">Discovery</h3>
                <p class="mt-2 text-xs leading-relaxed text-brand-muted">We listen to your goals, audit your needs, and map out a clear roadmap for success.</p>
            </div>
            <div class="relative rounded-2xl border border-warm-300/40 bg-white p-6 text-center shadow-card dark:border-slate-700/50 dark:bg-navy-800/80">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-primary text-lg font-bold text-white shadow-sm">2</div>
                <h3 class="mt-4 font-display text-sm text-brand-ink">Design</h3>
                <p class="mt-2 text-xs leading-relaxed text-brand-muted">Wireframes and prototypes bring your vision to life before any code is written.</p>
            </div>
            <div class="relative rounded-2xl border border-warm-300/40 bg-white p-6 text-center shadow-card dark:border-slate-700/50 dark:bg-navy-800/80">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-primary text-lg font-bold text-white shadow-sm">3</div>
                <h3 class="mt-4 font-display text-sm text-brand-ink">Development</h3>
                <p class="mt-2 text-xs leading-relaxed text-brand-muted">Clean, scalable code built with modern frameworks, tested at every milestone.</p>
            </div>
            <div class="relative rounded-2xl border border-warm-300/40 bg-white p-6 text-center shadow-card dark:border-slate-700/50 dark:bg-navy-800/80">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-primary text-lg font-bold text-white shadow-sm">4</div>
                <h3 class="mt-4 font-display text-sm text-brand-ink">Launch & Support</h3>
                <p class="mt-2 text-xs leading-relaxed text-brand-muted">Deployed with care, monitored continuously, and backed by ongoing support.</p>
            </div>
        </div>
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  TECHNOLOGIES                                                   ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
@php
    $allTech = $items->pluck('technologies')->filter()->flatten()->filter()->unique()->sort()->values();
@endphp
@if ($allTech->count() >= 4)
<section class="py-14 sm:py-20">
    <div class="mx-auto max-w-6xl px-4 sm:px-8">
        <div class="reveal text-center">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-4 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-primary">Tech Stack</span>
            <h2 class="mt-4 font-display text-2xl text-brand-ink sm:text-3xl">Technologies We Work With</h2>
            <p class="mx-auto mt-3 max-w-lg text-sm leading-relaxed text-brand-muted">
                We choose the right tools for every project — reliable, performant, and built to scale.
            </p>
        </div>
        <div class="reveal mt-10 flex flex-wrap justify-center gap-3">
            @foreach ($allTech->take(24) as $tech)
                <span class="rounded-xl border border-warm-300/40 bg-white px-4 py-2 text-sm font-medium text-brand-ink shadow-sm transition hover:border-brand-primary hover:shadow-md dark:border-slate-700/50 dark:bg-navy-800/80 dark:text-warm-200">
                    {{ $tech }}
                </span>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  CTA                                                            ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="border-t border-warm-300/40 bg-gradient-to-br from-navy-800 to-navy-900 py-16 sm:py-20">
    <div class="mx-auto max-w-4xl px-4 text-center sm:px-8">
        <div class="reveal">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent/10 px-4 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-accent">Let's Build Together</span>
            <h2 class="mt-5 font-display text-2xl text-white sm:text-3xl lg:text-4xl">Have a project in mind?</h2>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-relaxed text-warm-300/80 sm:text-base">
                Whether you need a custom application, a brand-new product, or a complete digital overhaul — we're ready to bring your vision to life.
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="{{ route('contact.show') }}" class="btn-primary">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                    Get in Touch
                </a>
                <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/20 bg-white/5 px-6 py-3 text-sm font-semibold text-white shadow-sm backdrop-blur transition hover:bg-white/10">
                    Browse Services
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function portfolioFilter() {
    return {
        query: '',
        freelancer: '',
        activeCategory: '',
        activeType: 'all',
        count: {{ $items->count() }},

        init() {
            this.updateCount();
            this.$watch('query', () => this.updateCount());
            this.$watch('freelancer', () => this.updateCount());
            this.$watch('activeCategory', () => this.updateCount());
            this.$watch('activeType', () => this.updateCount());
        },

        isVisible(title, author, type, categoriesText) {
            const q = this.query.trim().toLowerCase();
            const blob = (categoriesText || '').toLowerCase();
            const matchQuery = !q || title.toLowerCase().includes(q) || author.toLowerCase().includes(q) || blob.includes(q);
            const matchFreelancer = !this.freelancer || author === this.freelancer;
            const matchType = this.activeType === 'all' || this.activeType === type;
            const matchCategory = !this.activeCategory || blob.includes(this.activeCategory.toLowerCase());
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