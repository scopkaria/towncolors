<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Towncore') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{-- Dark mode anti-flash --}}
        <script>(function(){try{var s=localStorage.getItem('darkMode');var sys=window.matchMedia('(prefers-color-scheme: dark)').matches;if(s==='true'||(s===null&&sys)){document.documentElement.classList.add('dark');}}catch(e){}})();</script>
        @include('partials.theme-vars')
    </head>
    <body class="font-sans text-brand-ink antialiased transition-colors duration-300">
        <div class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid w-full max-w-6xl overflow-hidden rounded-[2rem] border border-white/70 bg-white/80 shadow-panel backdrop-blur-xl dark:border-slate-700/40 dark:bg-slate-900/85 xl:grid-cols-[1.08fr_0.92fr]">
                <section class="relative hidden overflow-hidden bg-navy-800 px-10 py-12 text-white xl:block">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(249,115,22,0.34),_transparent_32%),radial-gradient(circle_at_bottom_right,_rgba(255,255,255,0.08),_transparent_24%)]"></div>
                    <div class="relative flex h-full flex-col justify-between">
                        <div>
                            <a href="{{ url('/') }}" class="inline-flex items-center gap-4">
                                <x-site-logo
                                    icon-wrap-class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10"
                                    icon-class="h-7 w-7"
                                    name-class="block font-display text-2xl text-white"
                                    logo-class="h-12 w-auto object-contain brightness-0 invert"
                                >
                                    <x-slot name="subtitle">
                                        <span class="text-xs uppercase tracking-[0.3em] text-white/60">Premium operations dashboard</span>
                                    </x-slot>
                                </x-site-logo>
                            </a>
                            <div class="mt-16 max-w-lg space-y-5">
                                <p class="text-sm font-semibold uppercase tracking-[0.34em] text-accent">Designed for modern delivery teams</p>
                                <h1 class="font-display text-5xl leading-tight">A sharper workspace for admin, client, and freelancer collaboration.</h1>
                                <p class="text-base leading-8 text-white/70">Towncore pairs fast authentication with a premium dashboard shell, role-aware views, and a visual system built around warm orange energy.</p>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                <p class="text-xs uppercase tracking-[0.24em] text-accent/80">Focus</p>
                                <p class="mt-3 font-display text-xl">Role-based navigation</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                <p class="text-xs uppercase tracking-[0.24em] text-accent/80">Motion</p>
                                <p class="mt-3 font-display text-xl">Soft lift and fade interactions</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                <p class="text-xs uppercase tracking-[0.24em] text-accent/80">Style</p>
                                <p class="mt-3 font-display text-xl">Rounded cards with calm depth</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="px-6 py-8 sm:px-10 sm:py-10 lg:px-12">
                    <div class="mb-8 xl:hidden">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-4">
                            <x-site-logo
                                icon-wrap-class="flex h-12 w-12 items-center justify-center rounded-2xl bg-navy-800 text-white shadow-card"
                                icon-class="h-7 w-7"
                                name-class="block font-display text-2xl text-brand-ink"
                                logo-class="h-12 w-auto object-contain"
                            >
                                <x-slot name="subtitle">
                                    <span class="text-xs uppercase tracking-[0.3em] text-brand-muted">Premium operations dashboard</span>
                                </x-slot>
                            </x-site-logo>
                        </a>
                    </div>

                    <div class="page-fade mx-auto w-full max-w-md">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
