<x-public-layout title="Home">

<div id="tc-preloader" aria-hidden="true" class="tc-preloader fixed inset-0 z-[80] flex items-center justify-center bg-white/95 backdrop-blur-sm dark:bg-[#09090b]/95">
    <div class="tc-preloader__inner flex flex-col items-center gap-4 text-center">
        <div class="tc-preloader__logo">
            <x-site-logo
                icon-wrap-class="flex h-12 w-12 items-center justify-center rounded-xl bg-navy-800 text-white shadow-card dark:bg-warm-100 dark:text-slate-900"
                icon-class="h-7 w-7"
                name-class="font-display text-xl text-brand-ink dark:text-white"
                logo-class="h-12 w-auto object-contain"
            />
        </div>
        <p class="tc-preloader__label text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-muted">Town Colors</p>
    </div>
</div>

<div data-towncore-home class="tc-home">

{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  1 · HERO                                                       ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section id="home-hero" data-home-section data-home-hero class="tc-section relative overflow-hidden py-8 sm:py-10 lg:py-12">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-primary/8 blur-[80px] sm:h-[420px] sm:w-[420px]"></div>
        <div class="absolute -bottom-20 right-0 h-56 w-56 rounded-full bg-accent-light blur-[60px] sm:h-[340px] sm:w-[340px]"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-8">
        <div class="grid items-center gap-8 md:grid-cols-[0.95fr_1.05fr] md:gap-6">
                <div>
                    <span data-home-hero-item class="reveal inline-flex px-0 py-1 text-[13px] font-semibold uppercase tracking-[0.2em] text-brand-primary">
                        Digital solutions that drive growth
                    </span>

                    <h1 data-home-hero-item class="reveal reveal-delay-1 mt-4 font-display text-[2.7rem] font-bold leading-[1.08] text-brand-ink sm:text-[3.35rem] lg:text-[3.9rem]">
                        We Build Websites, Systems & Brands That
                        <span class="text-brand-primary">Grow</span>
                        Businesses
                    </h1>

                    <p data-home-hero-item class="reveal reveal-delay-2 mt-5 max-w-xl text-[1.05rem] leading-9 text-brand-muted sm:text-[1.05rem] sm:leading-8 lg:text-[1.15rem]">
                        Town Colors helps businesses grow through modern websites, smart systems, branding, printing, media production and digital marketing solutions.
                    </p>

                    <div data-home-hero-item class="reveal reveal-delay-3 mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                        @auth
                            @if (auth()->user()->role?->value === 'client')
                                <a href="{{ route('client.projects.create') }}" class="btn-primary w-full justify-center sm:w-auto">
                            @else
                                <a href="#quick-inquiry" class="btn-primary w-full justify-center sm:w-auto">
                            @endif
                        @else
                            <a href="#quick-inquiry" class="btn-primary w-full justify-center rounded-xl px-8 sm:w-auto">
                        @endauth
                            Start Your Project
                            <svg class="ml-2 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0-4 4m4-4H3" />
                            </svg>
                        </a>

                        <a href="{{ route('portfolio.public') }}" class="btn-secondary w-full justify-center rounded-xl px-8 sm:w-auto">
                            View Our Work
                            <svg class="ml-2 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div data-home-hero-item class="reveal reveal-delay-2 relative">
                    <div class="relative">
                        <img src="{{ asset('images/hero-section-tc.png') }}" alt="Town Colors hero visual" class="mx-auto h-auto w-full max-w-[46rem] object-contain md:max-w-none md:scale-[1.12] md:translate-x-10 md:-translate-y-8 lg:scale-[1.18] lg:translate-x-14 lg:-translate-y-10">
                    </div>
                </div>
        </div>

        <div data-home-hero-item class="reveal reveal-delay-4 mt-9 grid grid-cols-2 overflow-hidden rounded-2xl border border-warm-300/60 bg-white shadow-card sm:grid-cols-4">
                <div class="flex items-center gap-3 border-b border-r border-warm-300/60 px-4 py-4 sm:border-b-0 sm:px-6 sm:py-6">
                    <svg class="h-7 w-7 shrink-0 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4.5h12A1.5 1.5 0 0 1 19.5 6v12a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V6A1.5 1.5 0 0 1 6 4.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 15.5 10 13l2 2 4-4" /><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 11.5v4m4.5-6v6m4.5-3v3" /></svg>
                    <div>
                        <p class="font-display text-3xl text-brand-ink">100+</p>
                        <p class="text-[11px] text-brand-muted sm:text-sm">Projects Completed</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 border-b border-warm-300/60 px-4 py-4 sm:border-b-0 sm:border-r sm:px-6 sm:py-6">
                    <svg class="h-7 w-7 shrink-0 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4.5h12A1.5 1.5 0 0 1 19.5 6v12a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V6A1.5 1.5 0 0 1 6 4.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.25 11a1.75 1.75 0 1 0 0-3.5 1.75 1.75 0 0 0 0 3.5Zm5.5 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 16.5a2.75 2.75 0 0 1 5.5 0m1 0a1.75 1.75 0 0 1 3.5 0" /></svg>
                    <div>
                        <p class="font-display text-3xl text-brand-ink">50+</p>
                        <p class="text-[11px] text-brand-muted sm:text-sm">Happy Clients</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 border-r border-warm-300/60 px-4 py-4 sm:px-6 sm:py-6">
                    <svg class="h-7 w-7 shrink-0 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4.5h12A1.5 1.5 0 0 1 19.5 6v12a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V6A1.5 1.5 0 0 1 6 4.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m12 8.25 1.35 2.74 3.03.44-2.19 2.13.52 3.01L12 15.1l-2.71 1.47.52-3.01-2.19-2.13 3.03-.44L12 8.25Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 15.9v2.35L12 17.2l1.5 1.05V15.9" /></svg>
                    <div>
                        <p class="font-display text-3xl text-brand-ink">5+</p>
                        <p class="text-[11px] text-brand-muted sm:text-sm">Years of Experience</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-4 py-4 sm:px-6 sm:py-6">
                    <svg class="h-7 w-7 shrink-0 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4.5h12A1.5 1.5 0 0 1 19.5 6v12a1.5 1.5 0 0 1-1.5 1.5H6A1.5 1.5 0 0 1 4.5 18V6A1.5 1.5 0 0 1 6 4.5Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 12a3.75 3.75 0 1 1 7.5 0v1.25" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 12.5h.5a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1Zm7.5 0h.5a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1h-.5a1 1 0 0 1-1-1v-1a1 1 0 0 1 1-1Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 13.5v2h1.5" /></svg>
                    <div>
                        <p class="font-display text-3xl text-brand-ink">24/7</p>
                        <p class="text-[11px] text-brand-muted sm:text-sm">Support & Maintenance</p>
                    </div>
                </div>
            </div>
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  2 · SERVICES                                                   ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section data-home-section class="tc-section py-14 sm:py-20 lg:py-28" id="services">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">

        {{-- Section header --}}
        <div class="reveal mx-auto max-w-2xl text-center">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3.5 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-primary dark:border-accent/30 dark:bg-accent/10 sm:px-4 sm:py-1.5 sm:text-[11px] sm:tracking-[0.3em]">
                What We Do
            </span>
            <h2 class="mt-4 font-display text-2xl font-bold text-brand-ink sm:mt-5 sm:text-3xl lg:text-4xl">
                Complete digital solutions tailored to your business growth
            </h2>
            <p class="mt-3 text-sm leading-6 text-brand-muted sm:mt-4 sm:text-base sm:leading-7">
                At Town Colors, we provide strategy, engineering, creative production, and growth execution in one premium team.
            </p>
        </div>

        @php
            $homeServices = [
                [
                    'title' => 'Website Design & Development',
                    'slug'  => 'website-design',
                    'description' => 'We build modern, fast, and responsive websites designed to convert visitors into customers.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17 9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z" />',
                ],
                [
                    'title' => 'Custom Software & Systems Development',
                    'slug'  => 'custom-web-development',
                    'description' => 'We develop powerful systems including business management systems, hospital systems, and educational platforms.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />',
                ],
                [
                    'title' => 'Mobile App Development',
                    'slug'  => 'app-development',
                    'description' => 'We create high-performance mobile applications tailored for business operations and customer engagement.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />',
                ],
                [
                    'title' => 'Graphic Design',
                    'slug'  => 'graphic-design',
                    'description' => 'We design professional branding materials including logos, marketing designs, and digital assets. Printing is outsourced to trusted partners.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />',
                ],
                [
                    'title' => 'Photography & Videography',
                    'slug'  => 'photography-and-videography',
                    'description' => 'We produce high-quality visual content to elevate your brand presence across campaigns and product launches.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />',
                ],
                [
                    'title' => 'SEO (Search Engine Optimization)',
                    'slug'  => 'seo',
                    'description' => 'We optimize your business to rank higher on search engines and attract more qualified customers.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0 1 16.5 7.605" />',
                ],
                [
                    'title' => 'Content Creation',
                    'slug'  => 'content-creation',
                    'description' => 'We create engaging content that connects your brand with your audience and supports sustainable growth.',
                    'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />',
                ],
            ];
        @endphp

        <div class="mt-10 grid gap-5 sm:mt-14 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            @foreach ($homeServices as $index => $service)
                <a href="{{ route('services.show', $service['slug']) }}" class="reveal reveal-delay-{{ ($index % 4) + 1 }} group card-premium rounded-2xl border border-white/70 bg-white/90 p-5 shadow-card backdrop-blur-sm transition hover:-translate-y-0.5 hover:shadow-panel dark:border-slate-700/50 dark:bg-slate-800/80 sm:rounded-3xl sm:p-6 block no-underline">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-light text-brand-primary dark:bg-accent/10">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            {!! $service['icon'] !!}
                        </svg>
                    </div>
                    <h3 class="mt-4 font-display text-lg text-brand-ink transition duration-200 group-hover:text-brand-primary sm:text-xl">
                        {{ $service['title'] }}
                    </h3>
                    <p class="mt-2 text-sm leading-7 text-brand-muted">
                        {{ $service['description'] }}
                    </p>
                </a>
            @endforeach
        </div>

        <div class="reveal mt-10 text-center sm:mt-12">
            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-warm-300/50 bg-warm-100 px-5 py-2.5 text-sm font-semibold text-brand-ink shadow-sm transition duration-200 hover:border-brand-primary/40 hover:text-brand-primary sm:px-6 sm:py-3">
                Explore Full Services
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  3 · PORTFOLIO                                                  ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section data-home-section class="tc-section py-14 sm:py-20 lg:py-28" id="portfolio">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">

        {{-- Section header --}}
        <div class="reveal mx-auto max-w-2xl text-center">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3.5 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-primary sm:px-4 sm:py-1.5 sm:text-[11px] sm:tracking-[0.3em]">
                Work We've Done
            </span>
            <h2 class="mt-4 font-display text-2xl font-bold text-brand-ink sm:mt-5 sm:text-3xl lg:text-4xl">
                Results that speak louder than words
            </h2>
            <p class="mt-3 text-sm leading-6 text-brand-muted sm:mt-4 sm:text-base sm:leading-7">
                Take a look at the brands and businesses we've helped transform. Each project represents our commitment to excellence and real business growth.
            </p>
        </div>

        {{-- Portfolio grid --}}
        <div class="mt-10 grid gap-5 sm:mt-14 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            @forelse ($portfolios as $index => $item)
                <a href="{{ route('portfolio.show', $item->slug) }}" class="reveal reveal-delay-{{ ($index % 4) + 1 }} group relative overflow-hidden rounded-2xl border border-warm-300/40 bg-warm-100 shadow-card transition duration-300 hover:border-brand-primary/30 hover:shadow-md sm:rounded-3xl block no-underline">

                    {{-- Image --}}
                    <div class="relative h-44 overflow-hidden sm:h-56">
                        @if ($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}"
                                 alt="{{ $item->title }}"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                                 loading="lazy">
                        @else
                            <div class="flex h-full items-center justify-center bg-gradient-to-br from-accent-light to-warm-200">
                                <svg class="h-12 w-12 text-brand-primary/20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-4 sm:p-6">
                        <h3 class="font-display text-base text-brand-ink transition duration-200 group-hover:text-brand-primary sm:text-lg">
                            {{ $item->title }}
                        </h3>
                        <p class="mt-1.5 text-sm leading-6 text-brand-muted line-clamp-2">
                            {{ $item->description }}
                        </p>
                    </div>
                </a>
            @empty
                {{-- Placeholder items --}}
                @foreach (['Brand Identity Redesign', 'E-commerce Platform', 'Mobile Banking App', 'SaaS Dashboard', 'Marketing Website', 'Social Media App'] as $i => $name)
                    <div class="reveal reveal-delay-{{ ($i % 4) + 1 }} overflow-hidden rounded-2xl border border-warm-300/40 bg-warm-100 shadow-card sm:rounded-3xl">
                        <div class="flex h-44 items-center justify-center bg-gradient-to-br from-accent-light to-warm-200 sm:h-56">
                            <svg class="h-10 w-10 text-brand-primary/20 sm:h-12 sm:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="p-4 sm:p-5">
                            <h3 class="font-display text-base text-brand-ink sm:text-lg">{{ $name }}</h3>
                            <p class="mt-1.5 text-sm text-brand-muted">A beautifully crafted digital experience.</p>
                        </div>
                    </div>
                @endforeach
            @endforelse
        </div>

        {{-- View all link --}}
        <div class="reveal mt-10 text-center sm:mt-12">
            <a href="{{ url('/portfolio') }}" class="inline-flex items-center gap-2 rounded-2xl border border-warm-300/50 bg-warm-100 px-5 py-2.5 text-sm font-semibold text-brand-ink shadow-sm transition duration-200 hover:border-brand-primary/40 hover:text-brand-primary sm:px-6 sm:py-3">
                View Full Portfolio
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  4 · HOW IT WORKS                                               ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section data-home-section class="tc-section py-14 sm:py-20 lg:py-28" id="how-it-works">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">

        {{-- Section header --}}
        <div class="reveal mx-auto max-w-2xl text-center">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3.5 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-primary sm:px-4 sm:py-1.5 sm:text-[11px] sm:tracking-[0.3em]">
                Our Approach
            </span>
            <h2 class="mt-4 font-display text-2xl font-bold text-brand-ink sm:mt-5 sm:text-3xl lg:text-4xl">
                From vision to impact in four simple steps
            </h2>
            <p class="mt-3 text-sm leading-6 text-brand-muted sm:mt-4 sm:text-base sm:leading-7">
                We keep things transparent and straightforward. Here's exactly how we turn your ideas into real business results.
            </p>
        </div>

        {{-- Steps --}}
        @php
            $steps = [
                [
                    'number' => '01',
                    'title'  => 'Share Your Vision',
                    'desc'   => 'We start with a deep conversation—your goals, your timeline, your concerns. No jargon, no pressure, just understanding.',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />',
                ],
                [
                    'number' => '02',
                    'title'  => 'Strategic Planning',
                    'desc'   => 'We develop a detailed roadmap with clear deliverables, timelines, and investment. Everything upfront so you know exactly what to expect.',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
                ],
                [
                    'number' => '03',
                    'title'  => 'Expert Execution',
                    'desc'   => 'Our team designs and builds your project with regular updates, collaboration, and a commitment to quality at every step.',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />',
                ],
                [
                    'number' => '04',
                    'title'  => 'Launch & Grow',
                    'desc'   => 'We deliver your finished product and stick around to optimize, support, and help you scale for long-term success.',
                    'icon'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />',
                ],
            ];
        @endphp

        <div class="relative mt-10 grid grid-cols-2 gap-6 sm:mt-14 sm:gap-8 lg:mt-16 lg:grid-cols-4">
            {{-- Connection line (desktop) --}}
            <div class="absolute left-0 right-0 top-14 hidden h-px bg-gradient-to-r from-transparent via-warm-300 to-transparent dark:via-slate-700 lg:block"></div>

            @foreach ($steps as $index => $step)
                <div class="reveal reveal-delay-{{ $index + 1 }} relative text-center">
                    {{-- Icon circle --}}
                    <div class="relative mx-auto flex h-24 w-24 items-center justify-center sm:h-28 sm:w-28 lg:h-32 lg:w-32">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-accent-light to-warm-200/50 transition duration-300 dark:from-accent/10 dark:to-navy-800"></div>
                        <div class="relative flex h-14 w-14 items-center justify-center rounded-xl border border-white/70 bg-warm-100 shadow-card dark:border-slate-600/50 dark:bg-slate-800 sm:h-16 sm:w-16 sm:rounded-2xl lg:h-20 lg:w-20">
                            <svg class="h-6 w-6 text-brand-primary sm:h-7 sm:w-7 lg:h-8 lg:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                {!! $step['icon'] !!}
                            </svg>
                        </div>
                    </div>

                    {{-- Step number --}}
                    <span class="mt-3 inline-block font-display text-[10px] uppercase tracking-[0.25em] text-brand-primary sm:mt-4 sm:text-xs sm:tracking-[0.3em]">
                        Step {{ $step['number'] }}
                    </span>

                    {{-- Content --}}
                    <h3 class="mt-2 font-display text-base text-brand-ink sm:mt-3 sm:text-lg lg:text-xl">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-xs leading-5 text-brand-muted sm:mt-3 sm:text-sm sm:leading-7">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  5 · GLOBE FOCUS                                                ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section data-home-section data-home-globe class="tc-section py-14 sm:py-20 lg:py-28" id="globe-focus">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:gap-14">
        <div class="reveal">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3.5 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-primary sm:px-4 sm:py-1.5 sm:text-[11px] sm:tracking-[0.3em]">
                Global Reach
            </span>
            <h2 class="mt-4 font-display text-2xl font-bold text-brand-ink sm:mt-5 sm:text-3xl lg:text-4xl">
                Built in Africa, serving the world
            </h2>
            <p class="mt-4 max-w-xl text-sm leading-7 text-brand-muted sm:text-base sm:leading-8">
                Our studio is rooted in Arusha, Tanzania—a hub of innovation and creative excellence. Tap the globe to explore where strategy, design, and engineering come together to create digital magic.
            </p>

            <div class="reveal reveal-delay-1 mt-8 flex flex-wrap items-center gap-3">
                <button type="button" data-globe-zoom class="btn-primary">
                    Zoom to Arusha
                </button>
                <button type="button" data-globe-reset class="btn-secondary">
                    Reset View
                </button>
            </div>
        </div>

        <div class="reveal reveal-delay-2">
            <div class="tc-globe-panel relative mx-auto flex max-w-[28rem] items-center justify-center rounded-3xl border border-white/70 bg-white/90 p-5 shadow-panel sm:p-6">
                <div class="relative h-[18rem] w-[18rem] sm:h-[22rem] sm:w-[22rem]">
                    <canvas id="tc-globe-canvas" class="tc-globe-canvas h-full w-full" width="640" height="640" aria-label="Interactive globe centered on Arusha"></canvas>
                    <div class="tc-globe-map-overlay" aria-hidden="true"></div>
                </div>
                <button type="button" data-globe-target class="tc-globe-target" aria-label="Arusha, Tanzania">
                    <span class="tc-globe-target__dot"></span>
                    <span class="tc-globe-target__label">Arusha, Tanzania</span>
                </button>
            </div>
        </div>
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  6 · QUICK PROJECT INQUIRY                                      ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section data-home-section class="tc-section py-14 sm:py-20 lg:py-28" id="quick-inquiry">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        <div class="reveal mx-auto max-w-2xl text-center">
            <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3.5 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-primary sm:px-4 sm:py-1.5 sm:text-[11px] sm:tracking-[0.3em]">
                Get Started
            </span>
            <h2 class="mt-4 font-display text-2xl font-bold text-brand-ink sm:mt-5 sm:text-3xl lg:text-4xl">
                Ready to build something great?
            </h2>
            <p class="mt-3 text-sm leading-7 text-brand-muted sm:mt-4 sm:text-base sm:leading-8">
                Tell us about your vision. Whether you're starting from scratch or scaling up, we'll craft a custom strategy that delivers results. No obligations, just honest conversation.
            </p>
        </div>

        <div class="reveal mx-auto mt-10 max-w-2xl sm:mt-14" x-data="{ submitting: false }">

            {{-- Success state --}}
            @if (session('lead_success'))
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-8 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100">
                        <svg class="h-7 w-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <p class="mt-4 font-display text-xl text-emerald-800">Message received!</p>
                    <p class="mt-2 text-sm leading-7 text-emerald-700">{{ session('lead_success') }}</p>
                </div>
            @else
                <form method="POST" action="{{ route('leads.store') }}" @submit="submitting = true"
                      class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-panel sm:p-10">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                            <p class="font-semibold">Please fix the following:</p>
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid gap-5 sm:grid-cols-2">
                        {{-- Name --}}
                        <div>
                            <label for="lead_name" class="block text-sm font-semibold text-brand-ink">
                                Your name <span class="text-red-400">*</span>
                            </label>
                            <input id="lead_name" name="name" type="text" required
                                   value="{{ old('name') }}"
                                   placeholder="Jane Smith"
                                   class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 placeholder:text-warm-600 focus:border-brand-primary focus:ring-brand-primary @error('name') border-red-300 @enderror">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="lead_email" class="block text-sm font-semibold text-brand-ink">
                                Email address <span class="text-red-400">*</span>
                            </label>
                            <input id="lead_email" name="email" type="email" required
                                   value="{{ old('email') }}"
                                   placeholder="jane@company.com"
                                   class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 placeholder:text-warm-600 focus:border-brand-primary focus:ring-brand-primary @error('email') border-red-300 @enderror">
                        </div>
                    </div>

                    {{-- Project type --}}
                    <div class="mt-5">
                        <label for="lead_project_type" class="block text-sm font-semibold text-brand-ink">
                            Project type
                        </label>
                        <select id="lead_project_type" name="project_type"
                                class="mt-2 w-full rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 focus:border-brand-primary focus:ring-brand-primary @error('project_type') border-red-300 @enderror">
                            <option value="">— Select a category —</option>
                            @foreach (\App\Models\Lead::projectTypes() as $value => $label)
                                <option value="{{ $value }}" {{ old('project_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Message --}}
                    <div class="mt-5">
                        <label for="lead_message" class="block text-sm font-semibold text-brand-ink">
                            Tell us about your project <span class="text-red-400">*</span>
                        </label>
                        <textarea id="lead_message" name="message" rows="5" required
                                  placeholder="What are you building? What's your timeline? Any specific requirements?"
                                  class="mt-2 w-full resize-none rounded-2xl border-warm-300/50 bg-warm-100 px-4 py-3 text-sm leading-7 text-brand-ink shadow-sm transition duration-200 placeholder:text-warm-600 focus:border-brand-primary focus:ring-brand-primary @error('message') border-red-300 @enderror">{{ old('message') }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <div class="mt-6 flex items-center justify-between gap-4">
                        <p class="text-xs leading-5 text-brand-muted">
                            <svg class="mr-1 inline h-3.5 w-3.5 shrink-0 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                            We never share your information.
                        </p>
                        <button type="submit"
                                :disabled="submitting"
                                class="btn-primary shrink-0 disabled:cursor-not-allowed disabled:opacity-60">
                            <span x-show="!submitting">Send Inquiry</span>
                            <span x-show="submitting" x-cloak>Sending…</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
    ║  7 · CTA                                                        ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section data-home-section data-home-cta class="tc-section py-14 sm:py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        <div class="reveal relative overflow-hidden rounded-2xl bg-brand-primary px-5 py-12 text-center sm:rounded-[2rem] sm:px-10 sm:py-20 lg:px-12 lg:py-24">

            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute -left-16 -top-16 h-48 w-48 rounded-full bg-white/10 blur-[60px]"></div>
                <div class="absolute -bottom-16 -right-16 h-48 w-48 rounded-full bg-black/10 blur-[60px]"></div>
            </div>

            <div class="relative">
                <h2 class="mx-auto max-w-2xl font-display text-2xl font-bold text-white sm:text-3xl lg:text-4xl">
                    Ready to build something great?
                </h2>
                <p class="mx-auto mt-4 max-w-lg text-sm leading-6 text-white/80 sm:mt-5 sm:text-base sm:leading-7">
                    Stop settling for average. Partner with us and build something that moves the needle for your business. Let's make it happen.
                </p>

                <div class="mt-8 flex flex-col items-center gap-3 sm:mt-10 sm:flex-row sm:justify-center sm:gap-4">
                    <a href="{{ route('contact.show') }}" class="inline-flex items-center justify-center rounded-2xl bg-warm-100 px-6 py-3 text-[0.9375rem] font-semibold text-brand-primary shadow transition hover:-translate-y-0.5 hover:shadow-lg w-full sm:w-auto sm:text-base">
                        <svg class="mr-2 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z" />
                        </svg>
                        Contact Us
                    </a>
                    <a href="#quick-inquiry" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-white/30 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition duration-200 hover:bg-white/20 sm:w-auto sm:px-6">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Request a Proposal
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

</div>

</x-public-layout>
