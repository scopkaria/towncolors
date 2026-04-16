<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Towncore</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex flex-col items-center justify-center px-4 py-14 relative overflow-x-hidden"
      style="background:#07090e;">

    {{-- Background mesh --}}
    <div class="pointer-events-none fixed inset-0 -z-10"
         style="background-image:radial-gradient(rgba(255,255,255,0.03) 1px,transparent 1px);background-size:32px 32px;"></div>
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute top-0 left-1/4 h-[420px] w-[420px] -translate-y-1/3 rounded-full bg-indigo-600/12 blur-[130px]"></div>
        <div class="absolute bottom-0 right-1/4 h-[420px] w-[420px] translate-y-1/3 rounded-full bg-emerald-600/10 blur-[130px]"></div>
    </div>

    <div class="w-full max-w-3xl animate-[page-fade_0.6s_ease-out_both]">

        {{-- Logo --}}
        <div class="flex flex-col items-center mb-16">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 mb-12">
                <div class="h-12 w-12 flex items-center justify-center rounded-2xl border border-white/10 bg-white/5">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="font-display text-2xl font-bold text-white tracking-tight">Towncore</span>
            </a>
            <p class="text-[11px] font-bold uppercase tracking-[0.38em] text-white/30 mb-4">Welcome back</p>
            <h1 class="font-display text-4xl sm:text-5xl font-bold text-white text-center leading-[1.1] mb-4">
                Sign in to your workspace
            </h1>
            <p class="text-slate-500 text-sm max-w-sm text-center leading-7">
                Choose your account type to continue.
            </p>
        </div>

        {{-- Two role cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Client --}}
            <a href="{{ route('login.client') }}"
               class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/8 bg-white/[0.03] p-8
                      hover:border-indigo-500/40 hover:bg-indigo-950/25
                      transition-all duration-300 hover:-translate-y-1
                      hover:shadow-[0_20px_60px_-20px_rgba(99,102,241,0.3)]">

                {{-- Top corner accent --}}
                <div class="absolute top-0 right-0 h-24 w-24 rounded-full bg-indigo-500/8 blur-2xl group-hover:bg-indigo-500/16 transition-colors"></div>

                <div class="relative z-10">
                    <div class="mb-6 inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-indigo-500/20 bg-indigo-500/10 group-hover:border-indigo-500/40 group-hover:bg-indigo-500/20 transition-all">
                        <svg class="h-7 w-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>

                    <p class="mb-1 text-[10px] font-bold uppercase tracking-[0.3em] text-indigo-400/60">For Businesses</p>
                    <h2 class="font-display text-2xl font-bold text-white mb-3">Client</h2>
                    <p class="text-sm text-slate-500 leading-6">
                        Access your projects, review deliverables, track milestones, and manage your freelancers.
                    </p>

                    <div class="mt-8 flex items-center gap-2 text-sm font-semibold text-indigo-400 group-hover:gap-3 transition-all duration-200">
                        Sign in as Client
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>

            {{-- Freelancer --}}
            <a href="{{ route('login.freelancer') }}"
               class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/8 bg-white/[0.03] p-8
                      hover:border-emerald-500/40 hover:bg-emerald-950/20
                      transition-all duration-300 hover:-translate-y-1
                      hover:shadow-[0_20px_60px_-20px_rgba(16,185,129,0.25)]">

                <div class="absolute top-0 right-0 h-24 w-24 rounded-full bg-emerald-500/8 blur-2xl group-hover:bg-emerald-500/14 transition-colors"></div>

                <div class="relative z-10">
                    <div class="mb-6 inline-flex h-14 w-14 items-center justify-center rounded-2xl border border-emerald-500/20 bg-emerald-500/10 group-hover:border-emerald-500/40 group-hover:bg-emerald-500/20 transition-all">
                        <svg class="h-7 w-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>

                    <p class="mb-1 text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-400/60">For Professionals</p>
                    <h2 class="font-display text-2xl font-bold text-white mb-3">Freelancer</h2>
                    <p class="text-sm text-slate-500 leading-6">
                        Resume active projects, respond to briefs, track earnings, and communicate with clients.
                    </p>

                    <div class="mt-8 flex items-center gap-2 text-sm font-semibold text-emerald-400 group-hover:gap-3 transition-all duration-200">
                        Sign in as Freelancer
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>
        </div>

        {{-- Register link --}}
        <p class="text-center text-sm text-slate-600 mt-10">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-white/60 hover:text-white transition-colors">Create one</a>
        </p>
    </div>
</body>
</html>

<body class="font-sans antialiased bg-navy-800 min-h-screen flex flex-col items-center justify-center px-4 py-14 relative overflow-x-hidden">

    {{-- Ambient background glows --}}
    <div class="pointer-events-none fixed inset-0 -z-10">
        <div class="absolute -top-32 -left-32 h-[500px] w-[500px] rounded-full bg-blue-600/10 blur-[120px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[600px] w-[600px] rounded-full bg-violet-600/8 blur-[140px]"></div>
        <div class="absolute -bottom-32 -right-32 h-[500px] w-[500px] rounded-full bg-amber-500/8 blur-[120px]"></div>
    </div>

    <div class="w-full max-w-5xl animate-[page-fade_0.6s_ease-out_both]">

        {{-- Logo --}}
        <div class="flex flex-col items-center mb-14">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 mb-10">
                <div class="h-11 w-11 flex items-center justify-center rounded-xl bg-accent/20 border border-accent/25">
                    <svg class="h-6 w-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="font-display text-xl font-bold text-white tracking-tight">Towncore</span>
            </a>
            <p class="text-xs font-semibold uppercase tracking-[0.32em] text-accent/80 mb-3">Welcome back</p>
            <h1 class="font-display text-4xl sm:text-5xl font-bold text-white text-center leading-tight mb-4">
                Sign in to your workspace
            </h1>
            <p class="text-slate-400 text-base max-w-lg text-center leading-7">
                Pick your account type to continue. Each role has its own tailored workspace waiting for you.
            </p>
        </div>

        {{-- Role cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

            {{-- Client --}}
            <a href="{{ route('login.client') }}"
               class="group relative flex flex-col rounded-2xl border border-slate-700/60 bg-slate-900/60 p-7 backdrop-blur-sm
                      hover:border-blue-500/50 hover:bg-blue-950/30
                      transition-all duration-300 hover:-translate-y-1.5
                      hover:shadow-[0_24px_64px_-16px_rgba(59,130,246,0.28)]">
                <div class="mb-6 flex h-13 w-13 items-center justify-center rounded-xl bg-blue-500/15 border border-blue-500/20 group-hover:bg-blue-500/25 transition-colors">
                    <svg class="h-7 w-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-blue-400/70 mb-1.5">For Businesses</p>
                <h2 class="font-display text-xl font-bold text-white mb-3">Client</h2>
                <p class="text-sm text-slate-400 leading-6 flex-1">Access your project dashboard, review deliverables, track milestones, and communicate with your freelancers.</p>
                <div class="mt-7 flex items-center gap-2 text-sm font-semibold text-blue-400 group-hover:gap-3 transition-all duration-200">
                    Sign in as Client
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </a>

            {{-- Freelancer --}}
            <a href="{{ route('login.freelancer') }}"
               class="group relative flex flex-col rounded-2xl border border-slate-700/60 bg-slate-900/60 p-7 backdrop-blur-sm
                      hover:border-violet-500/50 hover:bg-violet-950/30
                      transition-all duration-300 hover:-translate-y-1.5
                      hover:shadow-[0_24px_64px_-16px_rgba(139,92,246,0.28)]">
                <div class="mb-6 flex h-13 w-13 items-center justify-center rounded-xl bg-violet-500/15 border border-violet-500/20 group-hover:bg-violet-500/25 transition-colors">
                    <svg class="h-7 w-7 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-violet-400/70 mb-1.5">For Professionals</p>
                <h2 class="font-display text-xl font-bold text-white mb-3">Freelancer</h2>
                <p class="text-sm text-slate-400 leading-6 flex-1">Jump back into your active projects, proposals, earnings dashboard, and client messages.</p>
                <div class="mt-7 flex items-center gap-2 text-sm font-semibold text-violet-400 group-hover:gap-3 transition-all duration-200">
                    Sign in as Freelancer
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </a>

            {{-- Super Admin --}}
            <a href="{{ route('login.admin') }}"
               class="group relative flex flex-col rounded-2xl border border-slate-700/60 bg-slate-900/60 p-7 backdrop-blur-sm
                      hover:border-amber-500/50 hover:bg-amber-950/20
                      transition-all duration-300 hover:-translate-y-1.5
                      hover:shadow-[0_24px_64px_-16px_rgba(245,158,11,0.22)]">
                <div class="mb-6 flex h-13 w-13 items-center justify-center rounded-xl bg-amber-500/15 border border-amber-500/20 group-hover:bg-amber-500/25 transition-colors">
                    <svg class="h-7 w-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-amber-400/70 mb-1.5">Platform Management</p>
                <h2 class="font-display text-xl font-bold text-white mb-3">Super Admin</h2>
                <p class="text-sm text-slate-400 leading-6 flex-1">Access the full admin control panel — users, analytics, billing, system settings, and platform configuration.</p>
                <div class="mt-7 flex items-center gap-2 text-sm font-semibold text-amber-400 group-hover:gap-3 transition-all duration-200">
                    Admin Sign In
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </a>
        </div>

        {{-- Register link --}}
        <p class="text-center text-sm text-slate-500 mt-10">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-accent hover:text-accent transition-colors">Create one</a>
        </p>
    </div>
</body>
</html>
