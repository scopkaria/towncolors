<x-public-layout :title="$page->meta_title ?: $page->title">

    @push('head')
    <meta name="description" content="{{ $page->meta_description ?: '' }}">
    <link rel="canonical" href="{{ route('about') }}">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
    <meta property="og:description" content="{{ $page->meta_description ?: '' }}">
    <meta property="og:url" content="{{ route('about') }}">
    <style>
        /* ── Quill content reset ── */
        .story-body h1,.story-body h2,.story-body h3{color:var(--color-ink);font-weight:700;margin:1.5rem 0 .5rem;line-height:1.3;}
        .story-body h2{font-size:1.5rem;}.story-body h3{font-size:1.25rem;}
        .story-body p{color:var(--color-muted);line-height:1.9;margin:.875rem 0;font-size:.9375rem;}
        .story-body ul,.story-body ol{padding-left:1.5rem;margin:.875rem 0;color:var(--color-muted);line-height:1.9;}
        .story-body li{margin:.25rem 0;}.story-body ul li{list-style-type:disc;}.story-body ol li{list-style-type:decimal;}
        .story-body blockquote{border-left:3px solid var(--color-primary);padding-left:1.25rem;margin:1.25rem 0;color:var(--color-muted);font-style:italic;}
        .story-body strong{font-weight:700;color:var(--color-ink);}

        /* ── Timeline ── */
        .tl-item{opacity:0;transform:translateY(24px);transition:opacity .55s ease,transform .55s ease;}
        .tl-item.visible{opacity:1;transform:none;}

        /* ── Reveal animation ── */
        .reveal{opacity:0;transform:translateY(28px);transition:opacity .6s ease,transform .6s ease;}
        .reveal.visible{opacity:1;transform:none;}
        .reveal-delay-1{transition-delay:.1s;}.reveal-delay-2{transition-delay:.2s;}.reveal-delay-3{transition-delay:.3s;}

        /* ── Ticker (clients) ── */
        @keyframes ticker{0%{transform:translateX(0);}100%{transform:translateX(-50%);}}
        .ticker-track{display:flex;width:max-content;animation:ticker 28s linear infinite;}
        .ticker-track:hover{animation-play-state:paused;}
    </style>
    @endpush

    @foreach ($page->sections as $section)

    {{-- ═════════════════════════════════════════════════════════ HERO ══ --}}
    @if ($section->type === 'hero')
    <section class="relative overflow-hidden bg-white py-20 sm:py-28 border-b border-stone-100"
             @if ($section->get('bg_media_id') && isset($medias[$section->get('bg_media_id')]))
             style="background-image:linear-gradient(to bottom,rgba(255,255,255,.88),rgba(255,255,255,.94)),url('{{ $medias[$section->get('bg_media_id')]->url }}');background-size:cover;background-position:center;"
             @endif>
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-24 -right-24 h-80 w-80 rounded-full bg-brand-primary/8 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-orange-50 blur-3xl"></div>
        </div>
        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <span class="mb-4 inline-block rounded-full border border-orange-200 bg-orange-50 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-primary reveal">
                About Us
            </span>
            <h1 class="font-display text-[1.875rem] font-bold leading-tight text-brand-ink sm:text-4xl lg:text-5xl reveal reveal-delay-1">
                {{ $section->get('title', $page->title) }}
            </h1>
            @if ($section->get('subtitle'))
            <p class="mx-auto mt-6 max-w-xl text-base leading-relaxed text-brand-muted reveal reveal-delay-2">
                {{ $section->get('subtitle') }}
            </p>
            @endif
        </div>
    </section>
    @endif


    {{-- ════════════════════════════════════════════════════════ STORY ══ --}}
    @if ($section->type === 'story')
    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="relative">
                {{-- decorative quote mark --}}
                <span class="pointer-events-none absolute -top-8 -left-6 font-serif text-[8rem] leading-none text-brand-primary/10 select-none" aria-hidden="true">&ldquo;</span>
                <div class="story-body reveal prose max-w-none">
                    {!! $section->get('content') !!}
                </div>
            </div>
        </div>
    </section>
    @endif


    {{-- ══════════════════════════════════════════════════════ TIMELINE ══ --}}
    @if ($section->type === 'timeline')
    @php $items = $section->get('items', []); @endphp
    <section class="bg-stone-50 py-20 dark:bg-stone-900/60 sm:py-28">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if ($section->get('heading'))
            <div class="mb-14 text-center reveal">
                <span class="mb-3 inline-block rounded-full border border-orange-200 bg-orange-50 px-4 py-1 text-xs font-semibold uppercase tracking-widest text-brand-primary">History</span>
                <h2 class="font-display text-3xl font-bold text-brand-ink sm:text-4xl">{{ $section->get('heading') }}</h2>
            </div>
            @endif

            @if (!empty($items))
            <div class="relative">
                {{-- vertical line --}}
                <div class="absolute left-[18px] top-2 bottom-2 w-0.5 bg-stone-200 dark:bg-stone-700 sm:left-1/2 sm:-ml-px"></div>

                <div class="space-y-10">
                    @foreach ($items as $i => $item)
                    <div class="tl-item relative pl-10 sm:pl-0 {{ $i % 2 === 0 ? 'sm:text-right sm:pr-1/2 sm:mr-8 sm:ml-0' : 'sm:pl-[calc(50%+2rem)] sm:ml-0' }}">
                        {{-- dot --}}
                        <div class="absolute left-0 top-1 flex h-9 w-9 items-center justify-center rounded-full border-2 border-brand-primary bg-white text-xs font-bold text-brand-primary shadow sm:left-1/2 sm:-ml-[18px]">
                            {{ $loop->iteration }}
                        </div>
                        @if ($item['year'] ?? null)
                        <p class="text-[11px] font-bold uppercase tracking-widest text-brand-primary">{{ $item['year'] }}</p>
                        @endif
                        @if ($item['label'] ?? null)
                        <p class="mt-0.5 font-display text-lg font-semibold text-brand-ink">{{ $item['label'] }}</p>
                        @endif
                        @if ($item['description'] ?? null)
                        <p class="mt-1 text-sm leading-relaxed text-brand-muted">{{ $item['description'] }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif


    {{-- ══════════════════════════════════════════════════════ SERVICES ══ --}}
    @if ($section->type === 'services')
    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-14 text-center reveal">
                <span class="mb-3 inline-block rounded-full border border-orange-200 bg-orange-50 px-4 py-1 text-xs font-semibold uppercase tracking-widest text-brand-primary">What We Do</span>
                <h2 class="font-display text-3xl font-bold text-brand-ink sm:text-4xl">{{ $section->get('heading', 'Our Services') }}</h2>
                @if ($section->get('intro'))
                <p class="mx-auto mt-5 max-w-2xl text-base leading-relaxed text-brand-muted">{{ $section->get('intro') }}</p>
                @endif
            </div>
            @if ($services->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $idx => $svc)
                <div class="reveal reveal-delay-{{ min($idx + 1, 3) }} group flex flex-col rounded-2xl border border-stone-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-orange-200 hover:shadow-md dark:border-stone-800 dark:bg-stone-900">
                    <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 text-brand-primary">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"/>
                        </svg>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-brand-ink">{{ $svc->name }}</h3>
                    @if ($svc->description)
                    <p class="mt-2 text-sm leading-relaxed text-brand-muted line-clamp-3">{{ $svc->description }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>
    @endif


    {{-- ═══════════════════════════════════════════════════════ VISION ══ --}}
    @if ($section->type === 'vision')
    <section class="bg-stone-50 py-24 sm:py-32">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            @if ($section->get('heading'))
            <h2 class="font-display text-3xl font-bold text-brand-ink sm:text-4xl lg:text-5xl reveal">
                {{ $section->get('heading') }}
            </h2>
            @endif
            @if ($section->get('content'))
            <p class="mx-auto mt-8 max-w-3xl text-lg leading-relaxed text-brand-muted reveal reveal-delay-1">
                {{ $section->get('content') }}
            </p>
            @endif
        </div>
    </section>
    @endif


    {{-- ════════════════════════════════════════════════════ COMMUNITY ══ --}}
    @if ($section->type === 'community')
    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-orange-100 bg-gradient-to-br from-orange-50 to-amber-50 dark:from-stone-900 dark:to-stone-900 dark:border-stone-800">
                <div class="grid items-center gap-8 p-8 sm:p-12 lg:grid-cols-2 lg:gap-16">
                    <div class="reveal">
                        @if ($section->get('heading'))
                        <h2 class="font-display text-3xl font-bold text-brand-ink sm:text-4xl">{{ $section->get('heading') }}</h2>
                        @endif
                        @if ($section->get('content'))
                        <p class="mt-5 text-base leading-relaxed text-brand-muted">{{ $section->get('content') }}</p>
                        @endif
                    </div>
                    @if ($section->get('link_url'))
                    <div class="flex justify-center lg:justify-end reveal reveal-delay-1">
                        <a href="{{ $section->get('link_url') }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-3 rounded-2xl bg-brand-primary px-8 py-4 font-semibold text-white shadow-lg shadow-orange-200 transition hover:bg-orange-600 hover:-translate-y-0.5 dark:shadow-none">
                            {{ $section->get('link_label', 'Learn More') }}
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @endif


    {{-- ═════════════════════════════════════════════════════ CLIENTS ══ --}}
    @if ($section->type === 'clients')
    @php $clientMediaIds = $section->get('media_ids', []); @endphp
    @if (!empty($clientMediaIds))
    <section class="border-y border-stone-100 py-14 dark:border-stone-800">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @if ($section->get('heading'))
            <p class="mb-8 text-center text-xs font-bold uppercase tracking-widest text-brand-muted reveal">
                {{ $section->get('heading') }}
            </p>
            @endif
            <div class="overflow-hidden">
                <div class="ticker-track">
                    @foreach ([1, 2] as $__)
                    @foreach ($clientMediaIds as $mid)
                    @if (isset($medias[$mid]))
                    <div class="mx-6 flex h-12 w-28 flex-shrink-0 items-center justify-center grayscale transition hover:grayscale-0">
                        <img src="{{ $medias[$mid]->url }}" alt="{{ $medias[$mid]->name }}" class="max-h-10 w-full object-contain">
                    </div>
                    @endif
                    @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif
    @endif


    {{-- ══════════════════════════════════════════════════════════ CTA ══ --}}
    @if ($section->type === 'cta')
    <section class="relative overflow-hidden bg-brand-primary py-20 sm:py-28">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-16 -right-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-16 -left-16 h-64 w-64 rounded-full bg-black/10 blur-3xl"></div>
        </div>
        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            @if ($section->get('title'))
            <h2 class="font-display text-3xl font-bold text-white sm:text-4xl lg:text-5xl reveal">
                {{ $section->get('title') }}
            </h2>
            @endif
            @if ($section->get('subtitle'))
            <p class="mx-auto mt-5 max-w-xl text-base leading-relaxed text-white/80 reveal reveal-delay-1">
                {{ $section->get('subtitle') }}
            </p>
            @endif
            @if ($section->get('button_url'))
            <div class="mt-10 reveal reveal-delay-2">
                <a href="{{ $section->get('button_url') }}"
                   class="inline-flex items-center gap-2 rounded-2xl bg-white px-8 py-4 font-semibold text-brand-primary shadow-xl transition hover:-translate-y-0.5 hover:shadow-2xl">
                    {{ $section->get('button_label', 'Get Started') }}
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

    @endforeach

    {{-- fallback when no sections configured yet --}}
    @if ($page->sections->isEmpty())
    <section class="py-28">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="font-display text-4xl font-bold text-brand-ink">{{ $page->title }}</h1>
            @if ($page->content)
            <div class="prose prose-stone mx-auto mt-8">{!! $page->content !!}</div>
            @endif
        </div>
    </section>
    @endif

    @push('scripts')
    <script>
    (function () {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.reveal, .tl-item').forEach(el => io.observe(el));
    })();
    </script>
    @endpush

</x-public-layout>
