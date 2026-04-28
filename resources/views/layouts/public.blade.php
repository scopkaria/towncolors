<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title . ' · ' . config('app.name', 'Towncore') : config('app.name', 'Towncore') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Dark mode anti-flash: runs before paint --}}
    <script>
        (function(){
            try{
                var s=localStorage.getItem('darkMode');
                var sys=window.matchMedia('(prefers-color-scheme: dark)').matches;
                if(s==='true'||(s===null&&sys)){document.documentElement.classList.add('dark');}
            }catch(e){}
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-vars')
    @stack('head')
</head>
<body class="public-site font-sans antialiased transition-colors duration-300">

{{-- ═══════════════════════════════════════════════════════
     SYNESTHETIC PARTICLES BACKGROUND
═══════════════════════════════════════════════════════ --}}
<div id="tsparticles" aria-hidden="true"></div>

@php
    $settings = \App\Models\Setting::instance();
    $navLinks = [
        ['label' => 'Home',      'url' => url('/'),                  'active' => request()->is('/')],
        ['label' => 'Services',  'url' => route('services.index'),   'active' => request()->is('services*')],
        ['label' => 'Shop',      'url' => route('shop.index'),       'active' => request()->is('shop')],
        ['label' => 'Cloud',     'url' => route('cloud.index'),      'active' => request()->is('cloud-services')],
        ['label' => 'Portfolio', 'url' => route('portfolio.public'), 'active' => request()->is('portfolio*')],
        ['label' => 'Blog',      'url' => route('blog.index'),       'active' => request()->is('blog*')],
        ['label' => 'FAQ',       'url' => route('faq.index'),        'active' => request()->is('faq')],
        ['label' => 'About',     'url' => route('about'),            'active' => request()->is('about')],
        ['label' => 'Contact',   'url' => route('contact.show'),     'active' => request()->is('contact')],
    ];

    $desktopNavLabels = ['Home', 'Services', 'Portfolio', 'About', 'Blog', 'Contact'];
    $desktopNavLinks = collect($navLinks)->filter(fn ($link) => in_array($link['label'], $desktopNavLabels, true))->values();
@endphp

{{-- ══════════════════════════════════════════════════════════
     HEADER
══════════════════════════════════════════════════════════ --}}
<header data-home-header x-data="{
        open: false,
        isDark: document.documentElement.classList.contains('dark'),
        toggleDark() {
            this.isDark = !this.isDark;
            document.documentElement.classList.toggle('dark', this.isDark);
            localStorage.setItem('darkMode', this.isDark.toString());
        }
    }"
    class="sticky top-0 z-40 border-b border-warm-300/40 bg-warm-100/95 backdrop-blur-md transition-colors duration-300
           dark:border-warm-400/[0.08] dark:bg-navy-900/90">
    <div class="border-b border-warm-300/40 bg-warm-200/60 dark:border-warm-400/[0.08] dark:bg-navy-900/80">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-5 py-2.5 text-xs sm:px-8 sm:text-sm">
            <p class="text-brand-muted dark:text-warm-500">
                Premium digital solutions for websites, systems, and growth.
            </p>

            <div class="hidden items-center gap-4 text-brand-muted md:flex dark:text-warm-500">
                <a href="{{ route('login.client') }}" class="transition hover:text-brand-ink dark:hover:text-warm-100">Login</a>
                <a href="{{ route('register.client') }}" class="transition hover:text-brand-ink dark:hover:text-warm-100">My Account</a>
                <span class="opacity-60">English</span>
                <span class="opacity-60">USD</span>
                <button type="button"
                        @click="toggleDark()"
                        :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-warm-400/40 bg-warm-100 text-brand-muted transition hover:border-brand-primary/40 hover:text-brand-primary dark:border-warm-400/[0.12] dark:bg-navy-800 dark:text-warm-500">
                    <svg x-show="!isDark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0112 21c-5.385 0-9.75-4.365-9.75-9.75 0-4.06 2.49-7.538 6.027-8.99a.75.75 0 01.986.91A7.5 7.5 0 0018.82 12.73a.75.75 0 01.91.986z" />
                    </svg>
                    <svg x-show="isDark" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364 6.364l-1.06-1.06M6.697 6.697l-1.06-1.06m12.727 0l-1.06 1.06M6.697 17.303l-1.06 1.06M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-5 px-5 py-5 sm:px-8">
        <a href="{{ url('/') }}"
           data-home-logo
           class="flex shrink-0 items-center gap-3 opacity-100 transition duration-200 hover:opacity-80">
            <x-site-logo
                icon-wrap-class="flex h-10 w-10 items-center justify-center rounded-xl bg-navy-800 text-white shadow-card dark:bg-warm-100 dark:text-slate-900"
                icon-class="h-6 w-6"
                name-class="font-display text-lg sm:text-xl text-brand-ink dark:text-slate-100"
                logo-class="h-10 w-auto object-contain"
            />
        </a>

        <nav data-home-menu class="hidden flex-1 items-center justify-center gap-1 lg:flex" aria-label="Main navigation">
            @foreach ($desktopNavLinks as $link)
                <a href="{{ $link['url'] }}"
                   class="relative rounded-xl px-3 py-2 text-sm font-semibold transition duration-200
                          {{ $link['active']
                              ? 'text-brand-primary'
                              : 'text-brand-muted hover:text-brand-ink' }}">
                    {{ $link['label'] }}
                    @if ($link['active'])
                        <span class="absolute bottom-0 left-1/2 h-0.5 w-8 -translate-x-1/2 rounded-full bg-brand-primary"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-5 xl:flex">
            <div class="text-right">
                <p class="text-xs uppercase tracking-[0.16em] text-brand-muted dark:text-warm-500">Call Us</p>
                <a href="tel:{{ $settings->phone ?: '+255758273300' }}" class="text-base font-bold text-brand-ink transition hover:text-brand-primary dark:text-warm-100">
                    {{ $settings->phone ?: '+255 758 273 300' }}
                </a>
            </div>
            <a href="{{ route('contact.show') }}" class="btn-primary rounded-xl px-7">
                Get a Quote
                <svg class="ml-1.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0-4 4m4-4H3" />
                </svg>
            </a>
        </div>

        <button type="button"
                @click="toggleDark()"
                :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
                class="rounded-xl border border-warm-400/40 bg-warm-100 p-2.5 text-brand-muted
                       shadow-sm transition duration-200 hover:bg-warm-200/60 hover:text-brand-ink
                       dark:border-warm-400/[0.08] dark:bg-navy-800 dark:text-warm-600 dark:hover:bg-warm-400/[0.05] dark:hover:text-warm-100 lg:hidden">
            <svg x-show="!isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0112 21c-5.385 0-9.75-4.365-9.75-9.75 0-4.06 2.49-7.538 6.027-8.99a.75.75 0 01.986.91A7.5 7.5 0 0018.82 12.73a.75.75 0 01.91.986z" />
            </svg>
            <svg x-show="isDark" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364 6.364l-1.06-1.06M6.697 6.697l-1.06-1.06m12.727 0l-1.06 1.06M6.697 17.303l-1.06 1.06M12 7.5a4.5 4.5 0 100 9 4.5 4.5 0 000-9z" />
            </svg>
        </button>

        <button type="button"
                class="rounded-xl border border-warm-400/40 bg-warm-100 p-2.5 text-brand-muted
                       shadow-sm transition duration-200 hover:bg-warm-200/60 hover:text-brand-ink
                       dark:border-warm-400/[0.08] dark:bg-navy-800 dark:text-warm-600 dark:hover:bg-warm-400/[0.05] dark:hover:text-warm-100 lg:hidden"
                :aria-expanded="open.toString()"
                aria-label="Toggle navigation"
                @click="open = !open">
            <svg x-show="!open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg x-show="open" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="border-y border-warm-300/40 bg-gradient-to-r from-brand-primary via-orange-500 to-brand-primary dark:border-warm-400/[0.08]">
        <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-5 py-3 sm:px-8 lg:flex-row lg:items-center lg:justify-between">
            <a href="{{ route('services.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-navy-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-navy-800 dark:bg-warm-100 dark:text-navy-900 dark:hover:bg-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                All Services
            </a>

            <form action="{{ route('portfolio.public') }}" method="GET" class="flex w-full overflow-hidden rounded-xl bg-white shadow-sm lg:max-w-3xl">
                <input name="q" type="text" placeholder="Keyword here..."
                       class="w-full border-0 bg-white px-4 py-2.5 text-sm text-brand-ink placeholder:text-brand-muted focus:outline-none focus:ring-0">
                <button type="submit" class="inline-flex items-center justify-center bg-navy-900 px-4 text-white transition hover:bg-navy-800">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>

            <div class="hidden items-center gap-2 lg:flex">
                <a href="{{ route('portfolio.public') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-navy-900 transition hover:scale-105" aria-label="View portfolio">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.25 2.25 0 015.25 5.25h13.5A2.25 2.25 0 0121 7.5v9A2.25 2.25 0 0118.75 18.75H5.25A2.25 2.25 0 013 16.5v-9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5l5.25-5.25a2.25 2.25 0 013.182 0L15 14.818l1.568-1.568a2.25 2.25 0 013.182 0L21 14.5" />
                    </svg>
                </a>
                <a href="{{ route('contact.show') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-navy-900 transition hover:scale-105" aria-label="Contact us">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h6.75m-6.75 3h4.5m-8.25 6.75h12A2.25 2.25 0 0019.125 17.25V6.75A2.25 2.25 0 0016.875 4.5h-12A2.25 2.25 0 002.625 6.75v10.5A2.25 2.25 0 004.875 19.5z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- ── Mobile drawer ── --}}
    <div x-cloak
         x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-1"
         class="border-t border-warm-300/40 bg-warm-100/95 px-5 pb-6 pt-3 backdrop-blur-xl md:hidden
                dark:border-warm-400/[0.08] dark:bg-navy-900/97">

        <nav class="space-y-1" aria-label="Mobile navigation">
            @foreach ($navLinks as $link)
                <a href="{{ $link['url'] }}"
                   class="flex items-center rounded-xl px-4 py-3 text-sm font-semibold
                          transition duration-200
                          {{ $link['active']
                              ? 'bg-accent-light text-brand-primary dark:bg-accent-light'
                              : 'text-brand-ink hover:bg-warm-200/60 hover:text-brand-primary dark:text-warm-100 dark:hover:bg-warm-400/[0.05] dark:hover:text-brand-primary' }}"
                   @click="open = false">
                    {{ $link['label'] }}
                    @if ($link['active'])
                        <span class="ml-auto h-2 w-2 rounded-full bg-brand-primary"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="mt-5 border-t border-warm-300/40 pt-5 dark:border-warm-400/[0.08]">
            <div class="flex gap-2">
                <a href="{{ route('login.client') }}" class="flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-warm-400/40 bg-warm-100 px-4 py-3 text-sm font-semibold text-brand-ink transition hover:border-accent hover:bg-accent-light hover:text-accent-hover dark:border-warm-400/[0.08] dark:bg-navy-800 dark:text-warm-100">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Login
                </a>
                <a href="{{ route('contact.show') }}" class="btn-primary flex-1 justify-center">
                    Get a Quote
                    <svg class="ml-1.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
            <div class="mt-3">
                <a href="{{ route('register.client') }}" class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-warm-300/60 bg-white px-4 py-3 text-sm font-semibold text-brand-ink transition hover:border-accent hover:text-brand-primary dark:border-warm-400/[0.08] dark:bg-navy-800 dark:text-warm-100">
                    Create account
                </a>
            </div>
        </div>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════════════════════ --}}
<main class="relative z-10">
    {{ $slot }}
</main>

{{-- ══════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════ --}}
<footer data-home-footer class="relative z-10 border-t border-warm-300/40 bg-warm-100/85 backdrop-blur-sm dark:bg-navy-900/90 dark:border-warm-400/[0.06]">
    <div class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:py-20">

        {{-- Top grid ──────────────────────────────────── --}}
        <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Brand ──────── --}}
            <div class="sm:col-span-2">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <x-site-logo
                        icon-wrap-class="flex h-10 w-10 items-center justify-center rounded-xl bg-accent-light text-brand-primary"
                        icon-class="h-6 w-6"
                        name-class="font-display text-xl text-brand-ink"
                        logo-class="h-10 w-auto object-contain brightness-0"
                    />
                </a>

                <p class="mt-5 max-w-xs text-sm leading-7 text-brand-muted">
                    {{ $settings->address
                        ?: 'A premium workspace for admin, client, and freelancer collaboration — delivering projects with clarity and speed.' }}
                </p>

                {{-- Social links --}}
                <div class="mt-6 flex gap-3">
                    @php
                        $socials = [
                            'twitter' => '<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>',
                            'linkedin' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>',
                            'github' => '<path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>',
                        ];
                    @endphp
                    @foreach ($socials as $name => $iconPath)
                        <a href="#"
                           class="flex h-9 w-9 items-center justify-center rounded-xl border border-warm-300/50
                                  bg-warm-200/50 text-brand-muted transition duration-200
                                  hover:border-brand-primary/50 hover:bg-brand-primary/10 hover:text-brand-primary"
                           aria-label="{{ ucfirst($name) }}">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"
                                 aria-hidden="true">{!! $iconPath !!}</svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Quick Links ──────── --}}
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[0.28em] text-brand-muted">
                    Quick Links
                </h3>
                <ul class="mt-5 space-y-3">
                    @foreach ($navLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}"
                               class="group flex items-center gap-2 text-sm text-brand-muted
                                      transition duration-200 hover:text-brand-ink">
                                <span class="h-px w-3 bg-warm-400 transition duration-200 group-hover:w-5 group-hover:bg-brand-primary"></span>
                                {{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact ──────── --}}
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-[0.28em] text-brand-muted">
                    Contact Us
                </h3>
                <ul class="mt-5 space-y-4">
                    @if ($settings->email)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center
                                         rounded-lg bg-brand-primary/15 text-brand-primary">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0
                                             002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <a href="mailto:{{ $settings->email }}"
                               class="break-all text-sm text-brand-muted transition hover:text-brand-ink">
                                {{ $settings->email }}
                            </a>
                        </li>
                    @endif

                    @if ($settings->phone)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center
                                         rounded-lg bg-brand-primary/15 text-brand-primary">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1
                                             1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516
                                             5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0
                                             01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </span>
                            <a href="tel:{{ $settings->phone }}"
                               class="text-sm text-brand-muted transition hover:text-brand-ink">
                                {{ $settings->phone }}
                            </a>
                        </li>
                    @endif

                    @if ($settings->address)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center
                                         rounded-lg bg-brand-primary/15 text-brand-primary">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827
                                             0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <span class="text-sm text-brand-muted">{{ $settings->address }}</span>
                        </li>
                    @endif

                    @if (!$settings->email && !$settings->phone && !$settings->address)
                        <li>
                            <a href="{{ url('/page/contact') }}"
                               class="inline-flex items-center gap-2 rounded-xl border border-warm-300/50
                                      bg-warm-200/50 px-4 py-2.5 text-sm text-brand-muted transition
                                      hover:border-brand-primary/40 hover:bg-brand-primary/10 hover:text-brand-primary">
                                Get in touch
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Newsletter strip ─────────────────────────── --}}
        <div class="my-12 rounded-2xl border border-warm-300/50 bg-warm-200/50 px-6 py-8 sm:px-10">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-semibold text-lg text-brand-ink">Stay in the loop</h3>
                    <p class="mt-1 text-sm text-brand-muted">Get updates on new services and articles.</p>
                </div>

                @if(session('newsletter_success'))
                    <p class="flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-3
                              text-sm font-medium text-green-700 sm:shrink-0">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('newsletter_success') }}
                    </p>
                @else
                    <form action="{{ route('newsletter.subscribe') }}" method="POST"
                          class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                        @csrf
                        <div class="flex-1 sm:w-72">
                            <input type="email" name="email" placeholder="your@email.com" required
                                   value="{{ old('email') }}"
                                   class="w-full rounded-xl border border-warm-300/50 bg-warm-100 px-4 py-2.5
                                          text-sm text-brand-ink placeholder-brand-muted/50 outline-none
                                          focus:border-brand-primary focus:ring-1 focus:ring-brand-primary
                                          @error('email') border-red-400 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                class="shrink-0 rounded-xl bg-brand-primary px-5 py-2.5 text-sm
                                       font-semibold text-white shadow-sm transition hover:opacity-90">
                            Subscribe
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Bottom bar ──────────────────────────────── --}}
        <div class="mt-16 flex flex-col items-center justify-between gap-4
                    border-t border-warm-300/40 pt-8 sm:flex-row">
            <p class="text-sm text-brand-muted">
                &copy; {{ date('Y') }} {{ config('app.name', 'Towncore') }}. All rights reserved.
            </p>
            <div class="flex items-center gap-6 text-sm text-brand-muted">
                <a href="{{ url('/page/privacy') }}" class="transition hover:text-brand-ink">Privacy Policy</a>
                <a href="{{ url('/page/terms') }}" class="transition hover:text-brand-ink">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')

{{-- ══════════════════════════════════════════════════════════
     LIVE CHAT WIDGET
══════════════════════════════════════════════════════════ --}}
<div x-data="liveChatWidget()" x-cloak class="fixed bottom-5 right-5 z-50 flex flex-col items-end gap-3">

    {{-- ── Chat window ── --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="flex w-[340px] sm:w-[380px] flex-col overflow-hidden rounded-2xl border border-warm-400/30 bg-warm-100 shadow-2xl
                dark:border-warm-400/[0.10] dark:bg-navy-800"
         style="max-height: calc(100vh - 120px);">

        {{-- Header --}}
        <div :class="isOnline 
                ? 'bg-gradient-to-r from-emerald-500 to-emerald-600' 
                : 'bg-gradient-to-r from-slate-400 to-slate-500'"
             class="flex items-center justify-between px-5 py-4 text-white transition-all duration-300">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/20">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-bold leading-tight">Live Chat</p>
                    <p class="flex items-center gap-2 text-[11px] font-medium text-white/80">
                        <span :class="isOnline ? 'bg-white' : 'bg-white/50'" class="inline-flex h-2 w-2 rounded-full animate-pulse"></span>
                        <span x-text="isOnline ? 'Online now' : 'Offline'"></span>
                    </p>
                </div>
            </div>
            <button @click="open = false" class="rounded-lg p-1 transition hover:bg-white/20">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>
        </div>

        {{-- Intro / Name+Email form --}}
        <template x-if="!sessionKey">
            <form @submit.prevent="startChat()" class="space-y-4 px-5 py-6">
                <p class="text-sm leading-relaxed text-brand-muted dark:text-warm-600">
                    Hi there! Enter your details to start chatting with our team.
                </p>
                <input x-model="visitorName" type="text" placeholder="Your name" required
                       class="w-full rounded-xl border border-warm-400/30 bg-warm-50 px-4 py-2.5 text-sm text-brand-ink outline-none transition
                              focus:border-accent focus:ring-2 focus:ring-accent/20
                              dark:border-warm-400/[0.10] dark:bg-navy-800 dark:text-warm-100 dark:focus:border-accent dark:focus:ring-accent/20" />
                <input x-model="visitorEmail" type="email" placeholder="your@email.com" required
                       class="w-full rounded-xl border border-warm-400/30 bg-warm-50 px-4 py-2.5 text-sm text-brand-ink outline-none transition
                              focus:border-accent focus:ring-2 focus:ring-accent/20
                              dark:border-warm-400/[0.10] dark:bg-navy-800 dark:text-warm-100 dark:focus:border-accent dark:focus:ring-accent/20" />
                <button type="submit" :disabled="starting"
                        class="w-full rounded-xl bg-accent px-4 py-3 text-sm font-semibold text-navy-800 shadow-sm transition hover:bg-accent-hover hover:text-warm-100 disabled:opacity-60">
                    <span x-text="starting ? 'Connecting…' : 'Start Chat'"></span>
                </button>
            </form>
        </template>

        {{-- Chat messages area --}}
        <template x-if="sessionKey">
            <div class="flex flex-1 flex-col">
                <div x-ref="chatMessages" class="flex-1 space-y-3 overflow-y-auto px-4 py-4" style="max-height: 320px; min-height: 200px;">
                    {{-- Waiting indicator --}}
                    <template x-if="messages.length === 0 && sessionStatus === 'waiting'">
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-accent-light dark:bg-accent-light">
                                <svg class="h-5 w-5 animate-pulse text-accent" fill="currentColor" viewBox="0 0 20 20">
                                    <circle cx="4" cy="10" r="2"/><circle cx="10" cy="10" r="2"/><circle cx="16" cy="10" r="2"/>
                                </svg>
                            </div>
                            <p class="text-sm text-brand-muted dark:text-warm-600">Waiting for an agent…</p>
                        </div>
                    </template>

                    {{-- Messages --}}
                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender_type === 'visitor' ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.sender_type === 'visitor'
                                    ? 'rounded-2xl rounded-br-md bg-accent px-4 py-2.5 text-sm text-navy-800 max-w-[80%]'
                                    : 'rounded-2xl rounded-bl-md bg-warm-300/40 px-4 py-2.5 text-sm text-brand-ink max-w-[80%] dark:bg-warm-400/[0.06] dark:text-warm-100'"
                                 x-text="msg.body"></div>
                        </div>
                    </template>
                </div>

                {{-- Input --}}
                <div class="border-t border-warm-300/40 px-4 py-3 dark:border-warm-400/[0.08]"
                     x-show="sessionStatus !== 'closed'">
                    <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                        <input x-model="newMessage" type="text" placeholder="Type a message…"
                               class="flex-1 rounded-xl border border-warm-400/30 bg-warm-50 px-4 py-2.5 text-sm text-brand-ink outline-none transition
                                      focus:border-accent focus:ring-2 focus:ring-accent/20
                                      dark:border-warm-400/[0.10] dark:bg-navy-800 dark:text-warm-100 dark:focus:border-accent dark:focus:ring-accent/20"
                               @keydown.enter="sendMessage()" />
                        <button type="submit" :disabled="!newMessage.trim()"
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-accent text-navy-800 shadow-sm transition hover:bg-accent-hover hover:text-warm-100 disabled:opacity-40">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Closed notice --}}
                <div x-show="sessionStatus === 'closed'" class="border-t border-warm-300/40 px-5 py-4 text-center dark:border-warm-400/[0.08]">
                    <p class="text-sm text-brand-muted dark:text-warm-600">This chat has been closed.</p>
                    <button @click="resetChat()" class="mt-2 text-sm font-semibold text-accent transition hover:text-accent-hover">Start a new chat</button>
                </div>
            </div>
        </template>
    </div>

    {{-- ── Floating trigger button ── --}}
    <button @click="open = !open"
            :class="isOnline
                ? 'bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-600/40'
                : 'bg-gradient-to-br from-slate-400 to-slate-500 shadow-lg shadow-slate-400/30 hover:shadow-slate-500/40'"
            class="group relative flex h-14 w-14 items-center justify-center rounded-full text-navy-800 shadow-lg transition-all duration-300
                   hover:scale-105 hover:shadow-xl dark:shadow-opacity-20"
            :aria-label="open ? 'Close chat' : 'Open live chat'">
        {{-- Online indicator dot --}}
        <span :class="isOnline ? 'bg-white' : 'bg-white/40'" 
              class="absolute top-0 right-0 h-3.5 w-3.5 rounded-full border-2 border-white/30 animate-pulse"></span>
        
        <svg x-show="!open" class="h-6 w-6 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
        </svg>
        <svg x-show="open" class="h-6 w-6 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<script>
function liveChatWidget() {
    return {
        open: false,
        sessionKey: localStorage.getItem('lc_session_key') || '',
        sessionStatus: '',
        visitorName: '',
        visitorEmail: '',
        newMessage: '',
        messages: [],
        lastMsgId: 0,
        pollTimer: null,
        statusTimer: null,
        starting: false,
        isOnline: true,

        init() {
            this.$watch('open', (val) => {
                if (val && this.sessionKey) this.startPolling();
                else this.stopPolling();
            });
            if (this.sessionKey) {
                this.fetchMessages();
            }
            // Check status immediately and then every 30 seconds
            this.checkChatStatus();
            this.statusTimer = setInterval(() => this.checkChatStatus(), 30000);
        },

        async checkChatStatus() {
            try {
                const res = await fetch('{{ route("live-chat.status") }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    this.isOnline = data.online ?? true;
                }
            } catch (e) {
                console.error('Chat status check error', e);
            }
        },

        async startChat() {
            this.starting = true;
            try {
                const res = await fetch('{{ route("live-chat.start") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        session_key: this.sessionKey || null,
                        name: this.visitorName,
                        email: this.visitorEmail,
                    }),
                });
                const data = await res.json();
                this.sessionKey = data.session_key;
                this.sessionStatus = data.status;
                localStorage.setItem('lc_session_key', data.session_key);
                this.startPolling();
            } catch (e) {
                console.error('Live chat start error', e);
            }
            this.starting = false;
        },

        async sendMessage() {
            const body = this.newMessage.trim();
            if (!body) return;
            this.newMessage = '';

            // Optimistic add
            const tempId = Date.now();
            this.messages.push({ id: tempId, sender_type: 'visitor', body });
            this.$nextTick(() => this.scrollToBottom());

            try {
                await fetch('{{ route("live-chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ session_key: this.sessionKey, body }),
                });
            } catch (e) {
                console.error('Send error', e);
            }
        },

        async fetchMessages() {
            if (!this.sessionKey) return;
            try {
                const url = new URL('{{ route("live-chat.messages") }}', location.origin);
                url.searchParams.set('session_key', this.sessionKey);
                if (this.lastMsgId) url.searchParams.set('after', this.lastMsgId);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) {
                    if (res.status === 404) { this.resetChat(); return; }
                    return;
                }
                const data = await res.json();
                this.sessionStatus = data.status;
                if (data.messages.length) {
                    // Merge new messages, avoid dupes
                    const existingIds = new Set(this.messages.map(m => m.id));
                    data.messages.forEach(m => {
                        if (!existingIds.has(m.id)) this.messages.push(m);
                    });
                    this.lastMsgId = data.messages[data.messages.length - 1].id;
                    this.$nextTick(() => this.scrollToBottom());
                }
                if (data.status === 'closed') this.stopPolling();
            } catch (e) {
                console.error('Fetch error', e);
            }
        },

        startPolling() {
            this.stopPolling();
            this.fetchMessages();
            this.pollTimer = setInterval(() => this.fetchMessages(), 4000);
        },

        stopPolling() {
            if (this.pollTimer) { clearInterval(this.pollTimer); this.pollTimer = null; }
        },

        scrollToBottom() {
            const el = this.$refs.chatMessages;
            if (el) el.scrollTop = el.scrollHeight;
        },

        resetChat() {
            this.stopPolling();
            this.sessionKey = '';
            this.sessionStatus = '';
            this.messages = [];
            this.lastMsgId = 0;
            this.newMessage = '';
            localStorage.removeItem('lc_session_key');
        },

        destroy() { 
            this.stopPolling();
            if (this.statusTimer) clearInterval(this.statusTimer);
        }
    };
}
</script>

{{-- tsParticles CDN --}}
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.12.0/tsparticles.bundle.min.js" defer></script>

{{-- Scroll-reveal: add class="reveal" (optionally reveal-delay-{1-4}) to any section --}}
<script>
    (function () {
        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('.reveal').forEach(function (el) {
                el.classList.add('is-visible');
            });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.reveal').forEach(function (el) {
            io.observe(el);
        });
    })();
</script>

{{-- Synesthetic Particles Initialization --}}
<script>
    (function initParticles() {
        function loadParticles() {
            if (typeof tsParticles === 'undefined') {
                setTimeout(loadParticles, 100);
                return;
            }
            var isDark = document.documentElement.classList.contains('dark');
            tsParticles.load('tsparticles', {
                detectRetina: true,
                fpsLimit: 60,
                background: { color: { value: 'transparent' } },
                interactivity: {
                    detectsOn: 'window',
                    events: {
                        onHover: { enable: true, mode: 'grab' },
                        onClick: { enable: false },
                        resize: true
                    },
                    modes: {
                        grab: {
                            distance: 180,
                            links: { opacity: 0.55, color: '#FFB162' }
                        }
                    }
                },
                particles: {
                    color: {
                        value: isDark
                            ? ['#FFB162', '#C9C1B1', '#38bdf8', '#EEE9DF']
                            : ['#FFB162', '#A35139', '#2C3B4D', '#C9C1B1']
                    },
                    links: {
                        color: '#FFB162',
                        distance: 145,
                        enable: true,
                        opacity: isDark ? 0.12 : 0.08,
                        width: 1,
                        triangles: { enable: false }
                    },
                    move: {
                        enable: true,
                        speed: 1.1,
                        direction: 'none',
                        random: true,
                        straight: false,
                        outModes: { default: 'bounce' }
                    },
                    number: { value: 58, density: { enable: true, area: 1000 } },
                    opacity: {
                        value: { min: isDark ? 0.12 : 0.08, max: isDark ? 0.35 : 0.24 },
                        animation: { enable: true, speed: 0.7, minimumValue: 0.1, sync: false }
                    },
                    shape: { type: 'circle' },
                    size: {
                        value: { min: 1, max: 3 },
                        animation: { enable: true, speed: 1.2, minimumValue: 0.3, sync: false }
                    }
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadParticles);
        } else {
            loadParticles();
        }

        /* Re-init particles on theme toggle for colour adaptation */
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('[\\@click*="toggleDark"], [x-on\\:click*="toggleDark"]');
            if (btn) {
                setTimeout(function () {
                    if (window.tsParticlesInstance) {
                        window.tsParticlesInstance.destroy();
                    }
                    loadParticles();
                }, 350);
            }
        });
    })();
</script>

</body>
</html>
