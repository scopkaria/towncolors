<x-public-layout title="Services">

    @php $settings = \App\Models\Setting::first(); @endphp

    @push('head')
    <meta name="description" content="Discover Town Colors services: website development, software systems, mobile apps, graphic design, media production, SEO, and content creation.">
    <link rel="canonical" href="{{ route('services.index') }}">
    @endpush

    <x-public-hero
        badge="What We Do"
        title="Scalable service systems managed from your backend"
        :subtitle="$settings?->heroSubtitle('service') ?: 'Each service card below is powered from your admin categories, including featured visuals, pricing range, duration, and gallery-driven detail pages.'"
        :media="$settings?->heroMediaUrl('service')"
    />

    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-8">
            @if ($categories->isEmpty())
                <div class="rounded-3xl border border-dashed border-warm-300/50 bg-warm-100/70 p-12 text-center backdrop-blur-sm dark:border-slate-700/50 dark:bg-navy-800/70">
                    <h2 class="font-display text-2xl text-brand-ink">No services added yet</h2>
                    <p class="mt-3 text-sm leading-7 text-brand-muted">Create services in Admin Categories to publish them here automatically.</p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($categories as $index => $category)
                        <article class="reveal reveal-delay-{{ ($index % 3) + 1 }} overflow-hidden rounded-3xl border border-warm-300/50 bg-warm-100/70 shadow-sm backdrop-blur-sm transition hover:-translate-y-0.5 hover:shadow-panel dark:border-slate-700/50 dark:bg-navy-800/70">
                            <a href="{{ route('services.show', $category) }}" class="block">
                                <div class="relative h-52 overflow-hidden" style="background: linear-gradient(135deg, {{ $category->color }}22, {{ $category->color }}08);">
                                    @if ($category->featured_image)
                                        <img src="{{ asset('storage/' . $category->featured_image) }}" alt="{{ $category->name }}" class="h-full w-full object-cover transition duration-500 hover:scale-105">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/10 to-transparent"></div>
                                    @endif
                                    <span class="absolute left-4 top-4 inline-flex rounded-full border border-white/25 bg-black/30 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.16em] text-white backdrop-blur-sm">
                                        {{ $category->projects_count }} Project{{ $category->projects_count !== 1 ? 's' : '' }}
                                    </span>
                                </div>

                                <div class="p-6">
                                    <h2 class="font-display text-2xl text-brand-ink">{{ $category->name }}</h2>
                                    <p class="mt-3 line-clamp-3 text-sm leading-7 text-brand-muted">{{ $category->description ?: 'Professional service delivery tailored to your business workflow and growth targets.' }}</p>

                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @if ($category->price_range)
                                            <span class="rounded-full border border-warm-300/50 bg-warm-100/70 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-brand-muted dark:border-slate-700/50 dark:bg-navy-800/70">{{ $category->price_range }}</span>
                                        @endif
                                        @if ($category->estimated_duration)
                                            <span class="rounded-full border border-warm-300/50 bg-warm-100/70 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-brand-muted dark:border-slate-700/50 dark:bg-navy-800/70">{{ $category->estimated_duration }}</span>
                                        @endif
                                    </div>

                                    <div class="mt-5 inline-flex items-center gap-2 text-sm font-semibold" style="color: {{ $category->color }}">
                                        Explore service
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="border-t border-warm-300/40 bg-warm-200/50 py-16 dark:border-slate-700/40 dark:bg-navy-900/60">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-8">
            <h2 class="reveal font-display text-3xl font-bold text-brand-ink sm:text-4xl">Need a tailored execution plan?</h2>
            <p class="reveal reveal-delay-1 mt-4 text-base leading-8 text-brand-muted">
                Tell us your goals and we will propose the right service mix, rollout timeline, and execution strategy.
            </p>
            <div class="reveal reveal-delay-2 mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('contact.show') }}" class="btn-primary">Get Started</a>
                <a href="{{ route('portfolio.public') }}" class="btn-secondary">View Our Work</a>
            </div>
        </div>
    </section>

</x-public-layout>
