<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Towncore') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />
        {{-- Dark mode anti-flash --}}
        <script>(function(){try{var s=localStorage.getItem('darkMode');var sys=window.matchMedia('(prefers-color-scheme: dark)').matches;if(s==='true'||(s===null&&sys)){document.documentElement.classList.add('dark');}}catch(e){}})();</script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.theme-vars')
    </head>
    <body class="font-sans antialiased transition-colors duration-300"
          x-data="{
            isDark: document.documentElement.classList.contains('dark'),
            toggleDark() {
                this.isDark = !this.isDark;
                document.documentElement.classList.toggle('dark', this.isDark);
                localStorage.setItem('darkMode', this.isDark.toString());
            }
          }">
        <div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto flex min-h-[calc(100vh-3rem)] max-w-7xl flex-col rounded-[2rem] border border-white/70 bg-white/75 shadow-panel backdrop-blur-xl dark:border-slate-700/40 dark:bg-slate-900/80">
                <header class="flex flex-wrap items-center justify-between gap-4 px-6 py-6 sm:px-8 lg:px-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-4">
                        <x-site-logo
                            icon-wrap-class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-white shadow-card"
                            icon-class="h-7 w-7"
                            name-class="block font-display text-2xl text-brand-ink"
                            logo-class="h-12 w-auto object-contain"
                        >
                            <x-slot name="subtitle">
                                <span class="text-xs uppercase tracking-[0.3em] text-brand-muted">Role-aware SaaS dashboard</span>
                            </x-slot>
                        </x-site-logo>
                    </a>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="toggleDark()"
                                class="flex h-10 w-10 items-center justify-center rounded-xl border border-stone-200 bg-white text-brand-muted transition hover:border-orange-300 hover:bg-orange-50 hover:text-brand-primary dark:border-slate-600 dark:bg-slate-800 dark:text-slate-400 dark:hover:border-orange-400 dark:hover:bg-orange-500/15 dark:hover:text-brand-primary"
                                :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'">
                            <svg x-show="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364-.707-.707M6.343 6.343l-.707-.707m12.728 0-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg x-show="!isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                        <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Get started</a>
                        @endif
                    </div>
                </header>

                <main class="grid flex-1 gap-8 px-6 pb-8 pt-4 sm:px-8 lg:grid-cols-[1.1fr_0.9fr] lg:px-10 lg:pb-10 lg:pt-8">
                    <section class="flex flex-col justify-center">
                        <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                            Built for admin, client, and freelancer teams
                        </span>
                        <h1 class="mt-6 max-w-3xl font-display text-5xl leading-tight text-brand-ink sm:text-6xl">
                            A cleaner command center for project delivery, communication, and billing.
                        </h1>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-brand-muted">
                            Towncore packages Laravel Breeze authentication, role-aware routing, and a premium Tailwind interface into a workspace that feels polished from the first login.
                        </p>

                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            <a href="{{ route('register') }}" class="btn-primary">Create an account</a>
                            <a href="{{ route('login') }}" class="btn-secondary">Explore the login flow</a>
                        </div>

                        <div class="mt-10 grid gap-4 md:grid-cols-3">
                            <article class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                                <p class="text-xs uppercase tracking-[0.24em] text-brand-primary">Admin</p>
                                <h2 class="mt-3 font-display text-2xl text-brand-ink">Control the platform</h2>
                                <p class="mt-3 text-sm leading-7 text-brand-muted">See account health, delivery risk, and revenue signals in one place.</p>
                            </article>
                            <article class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                                <p class="text-xs uppercase tracking-[0.24em] text-brand-primary">Client</p>
                                <h2 class="mt-3 font-display text-2xl text-brand-ink">Stay in sync</h2>
                                <p class="mt-3 text-sm leading-7 text-brand-muted">Track milestones, review messages, and understand invoices clearly.</p>
                            </article>
                            <article class="card-premium rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                                <p class="text-xs uppercase tracking-[0.24em] text-brand-primary">Freelancer</p>
                                <h2 class="mt-3 font-display text-2xl text-brand-ink">Protect your focus</h2>
                                <p class="mt-3 text-sm leading-7 text-brand-muted">Manage briefs, approvals, and payouts without unnecessary friction.</p>
                            </article>
                        </div>
                    </section>

                    <section class="grid gap-4 content-center">
                        <article class="rounded-[2rem] bg-slate-950 p-6 text-white shadow-panel sm:p-8">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.3em] text-orange-200/80">Preview</p>
                                    <h2 class="mt-3 font-display text-3xl">Modern dashboard shell</h2>
                                </div>
                                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.24em] text-white/70">Responsive</span>
                            </div>

                            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                    <p class="text-sm text-white/60">Sidebar</p>
                                    <p class="mt-3 font-display text-2xl">Projects, messages, invoices, and dashboard links stay anchored.</p>
                                </div>
                                <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                    <p class="text-sm text-white/60">Interaction</p>
                                    <p class="mt-3 font-display text-2xl">Fade-in transitions and lift-on-hover keep the interface feeling alive.</p>
                                </div>
                            </div>
                        </article>

                        <article class="rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-card sm:p-8">
                            <p class="text-xs uppercase tracking-[0.3em] text-brand-primary">Starter credentials</p>
                            <h2 class="mt-3 font-display text-3xl text-brand-ink">Admin seed included</h2>
                            <div class="mt-5 space-y-3 text-sm leading-7 text-brand-muted">
                                <p>Email: <span class="font-semibold text-brand-ink">admin@towncore.local</span></p>
                                <p>Password: <span class="font-semibold text-brand-ink">password</span></p>
                            </div>
                        </article>
                    </section>
                </main>
            </div>
        </div>
    </body>
</html>
