<x-public-layout title="Portfolio">

@push('head')
<style>
    /* Subtle stagger offset for middle column on large screens */
    @media (min-width: 1024px) {
        .portfolio-card:nth-child(3n+2) { margin-top: 1.5rem; }
    }
</style>
@endpush

{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  HERO                                                           ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="relative overflow-hidden bg-white py-16 sm:py-24 border-b border-stone-100">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-primary/8 blur-[80px]"></div>
        <div class="absolute -bottom-16 right-0 h-56 w-56 rounded-full bg-orange-50 blur-[60px]"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="reveal inline-flex rounded-full border border-orange-200 bg-orange-50 px-3.5 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-primary sm:px-4 sm:py-1.5 sm:text-[11px] sm:tracking-[0.3em]">
                Our Work
            </span>
            <h1 class="reveal reveal-delay-1 mt-5 font-display text-[1.75rem] font-bold leading-[1.15] text-brand-ink sm:mt-6 sm:text-4xl lg:text-5xl">
                Portfolio
            </h1>
            <p class="reveal reveal-delay-2 mt-4 text-[0.9375rem] leading-7 text-brand-muted sm:text-lg sm:leading-8">
                A curated showcase of our delivered projects — crafted with precision and care.
            </p>
        </div>
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
        <div class="reveal mb-8 flex flex-col gap-4 sm:mb-10 sm:flex-row sm:items-center sm:gap-6">

            {{-- Search --}}
            <div class="relative flex-1 max-w-xs">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-brand-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input
                    type="text"
                    x-model="query"
                    placeholder="Search projects…"
                    class="w-full rounded-2xl border border-stone-200 bg-white py-2.5 pl-10 pr-9 text-sm text-brand-ink shadow-sm transition focus:border-brand-primary focus:outline-none focus:ring-1 focus:ring-brand-primary dark:border-slate-600/60 dark:bg-slate-800">
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
                            : 'border-stone-200 bg-white text-brand-muted hover:border-orange-200 hover:text-brand-primary dark:border-slate-600 dark:bg-slate-800 dark:hover:border-orange-400'"
                        class="rounded-xl border px-3.5 py-1.5 text-xs font-semibold transition duration-200">
                        All
                    </button>
                    @foreach ($freelancers as $fl)
                        <button
                            @click="freelancer = @js($fl->name)"
                            :class="freelancer === @js($fl->name)
                                ? 'border-brand-primary bg-brand-primary text-white shadow-[0_0_0_4px_rgba(249,115,22,0.12)]'
                                : 'border-stone-200 bg-white text-brand-muted hover:border-orange-200 hover:text-brand-primary'"
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

        @if ($items->isEmpty())
            {{-- Empty state --}}
            <div class="reveal flex flex-col items-center justify-center py-28 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-stone-100 bg-white shadow-card">
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
                <button @click="query = ''; freelancer = ''" class="mt-4 text-sm font-semibold text-brand-primary hover:underline">
                    Clear all filters
                </button>
            </div>

            {{-- Card grid --}}
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $index => $item)
                    <article
                        class="portfolio-card group relative flex flex-col overflow-hidden rounded-2xl border border-stone-100 bg-white shadow-card transition-all duration-300 ease-out dark:border-slate-700/50 dark:bg-slate-800/80 sm:rounded-3xl"
                        style="--delay: {{ ($index % 3) * 60 }}ms"
                        x-show="isVisible(@js($item->title), @js($item->freelancer?->name ?? ''))"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">

                        {{-- ── Image ── --}}
                        <div class="relative h-52 shrink-0 overflow-hidden bg-gradient-to-br from-orange-50 to-amber-50 sm:h-56">
                            @if ($item->image_path)
                                <img src="{{ asset('storage/' . $item->image_path) }}"
                                     alt="{{ $item->title }}"
                                     loading="lazy"
                                     class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.07]">
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <svg class="h-14 w-14 text-orange-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="0.8">
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
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/90 font-display text-sm font-bold text-brand-primary shadow ring-1 ring-stone-200/50">
                                        {{ strtoupper(substr($item->freelancer->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold text-white drop-shadow">{{ $item->freelancer->name }}</p>
                                        <p class="text-[10px] text-white/80">Creator</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Date badge --}}
                            <div class="absolute right-3 top-3">
                                <span class="rounded-xl border border-white/10 bg-black/30 px-2.5 py-1 text-[10px] font-medium text-white/80 backdrop-blur-sm">
                                    {{ $item->created_at->format('M Y') }}
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

                            @if ($item->description)
                                <p class="mt-2 flex-1 text-sm leading-6 text-brand-muted line-clamp-3">
                                    {{ $item->description }}
                                </p>
                            @else
                                <div class="flex-1"></div>
                            @endif

                            {{-- Footer --}}
                            <div class="mt-4 flex items-center justify-between gap-2 border-t border-stone-100 pt-4">
                                @if ($item->freelancer)
                                    <div class="flex min-w-0 items-center gap-2">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-orange-50 font-display text-xs font-bold text-brand-primary">
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
<section class="border-t border-stone-100 bg-white/60 py-12 sm:py-16">
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
        count:     {{ $items->count() }},

        init() {
            this.updateCount();
            this.$watch('query',      () => this.updateCount());
            this.$watch('freelancer', () => this.updateCount());
        },

        isVisible(title, author) {
            const q = this.query.trim().toLowerCase();
            const matchQuery      = !q || title.toLowerCase().includes(q) || author.toLowerCase().includes(q);
            const matchFreelancer = !this.freelancer || author === this.freelancer;
            return matchQuery && matchFreelancer;
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
