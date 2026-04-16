<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin Registration — Towncore</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#080c12] min-h-screen flex items-center justify-center p-4 lg:p-0">

    <div class="w-full h-full lg:min-h-screen flex flex-col lg:flex-row max-w-[1200px] mx-auto lg:max-w-none lg:fixed lg:inset-0">

        {{-- ── Left decorative panel ──────────────────────────────────────── --}}
        <aside class="relative hidden lg:flex lg:w-[46%] xl:w-[42%] flex-col justify-between overflow-hidden bg-[#07090f] px-12 py-12">

            {{-- Fine grid texture --}}
            <div class="pointer-events-none absolute inset-0"
                 style="background-image:linear-gradient(rgba(245,158,11,0.06) 1px,transparent 1px),linear-gradient(90deg,rgba(245,158,11,0.06) 1px,transparent 1px);background-size:40px 40px;"></div>

            {{-- Amber glow blobs --}}
            <div class="pointer-events-none absolute -bottom-40 -left-20 h-[500px] w-[500px] rounded-full bg-amber-500/15 blur-[120px]"></div>
            <div class="pointer-events-none absolute top-0 right-0 h-[280px] w-[280px] rounded-full bg-amber-400/6 blur-[80px]"></div>

            {{-- Corner accent line --}}
            <div class="absolute top-0 left-0 h-[2px] w-32 bg-gradient-to-r from-amber-500/60 to-transparent"></div>
            <div class="absolute top-0 left-0 h-32 w-[2px] bg-gradient-to-b from-amber-500/60 to-transparent"></div>

            {{-- Top brand --}}
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-amber-500/15 border border-amber-500/30">
                        <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="font-display text-lg font-bold text-white tracking-tight">Towncore</span>
                </a>
            </div>

            {{-- Main copy --}}
            <div class="relative z-10 space-y-8 my-auto py-16">
                <div>
                    <span class="inline-block rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.3em] text-amber-300">
                        Super Admin · Restricted Access
                    </span>
                </div>
                <h1 class="font-display text-4xl xl:text-5xl font-bold leading-tight text-white">
                    Platform command<br>
                    <span class="bg-gradient-to-r from-amber-300 to-amber-500 bg-clip-text text-transparent">centre.</span>
                </h1>
                <p class="text-base leading-8 text-slate-400 max-w-sm">
                    Full visibility and control over every user, project, transaction, and system setting across the entire platform.
                </p>

                {{-- Feature list --}}
                <div class="space-y-4 pt-2">
                    @foreach ([
                        ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'text' => 'Full user & role management'],
                        ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'text' => 'Platform analytics & reporting'],
                        ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'text' => 'Billing, payout & fee management'],
                        ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'text' => 'System settings & configuration'],
                    ] as $feature)
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-500/15 border border-amber-500/20">
                            <svg class="h-3.5 w-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-sm leading-7 text-slate-300">{{ $feature['text'] }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Security note --}}
                <div class="rounded-xl border border-amber-500/15 bg-amber-500/5 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-xs leading-5 text-amber-200/70">
                            Admin registration requires a valid invite code. Contact your platform owner to obtain one.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Bottom decoration --}}
            <div class="relative z-10 flex items-center gap-4">
                <div class="h-px flex-1 bg-gradient-to-r from-amber-500/30 to-transparent"></div>
                <span class="text-[10px] uppercase tracking-[0.25em] text-amber-500/50">Restricted portal</span>
                <div class="h-px flex-1 bg-gradient-to-l from-amber-500/30 to-transparent"></div>
            </div>
        </aside>

        {{-- ── Right panel / Form ──────────────────────────────── --}}
        <main class="flex flex-1 flex-col items-center justify-center overflow-y-auto bg-[#080c12] px-5 py-12 sm:px-8 lg:px-14 xl:px-20">

            {{-- Mobile logo --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <div class="h-9 w-9 flex items-center justify-center rounded-xl bg-amber-500/20 border border-amber-500/25">
                    <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="font-display text-lg font-bold text-white">Towncore</span>
            </div>

            <div class="w-full max-w-md animate-[page-fade_0.55s_ease-out_both]">

                {{-- Breadcrumb --}}
                <div class="mb-8">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-300 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to role selection
                    </a>
                </div>

                {{-- Heading --}}
                <div class="mb-8 space-y-2">
                    <div class="inline-flex items-center gap-2 rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-amber-300">Super Admin · Restricted</span>
                    </div>
                    <h2 class="font-display text-3xl font-bold text-white">Admin portal access</h2>
                    <p class="text-sm text-slate-400 leading-6">You must provide a valid admin invite code to complete registration.</p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('register.admin') }}" class="space-y-5" novalidate>
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">Full Name</label>
                        <input id="name" name="name" type="text"
                               value="{{ old('name') }}"
                               required autofocus autocomplete="name"
                               placeholder="Your full name"
                               class="block w-full rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-amber-500/70 focus:ring-2 focus:ring-amber-500/15 focus:outline-none transition" />
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">Email Address</label>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}"
                               required autocomplete="username"
                               placeholder="admin@towncore.io"
                               class="block w-full rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-amber-500/70 focus:ring-2 focus:ring-amber-500/15 focus:outline-none transition" />
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="username" class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">Username</label>
                            <input id="username" name="username" type="text"
                                   value="{{ old('username') }}"
                                   required autocomplete="nickname"
                                   placeholder="admin_ops"
                                   class="block w-full rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder-slate-600
                                          focus:border-amber-500/70 focus:ring-2 focus:ring-amber-500/15 focus:outline-none transition" />
                            @error('username')
                                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">Phone (optional)</label>
                            <input id="phone" name="phone" type="text"
                                   value="{{ old('phone') }}"
                                   autocomplete="tel"
                                   placeholder="+255..."
                                   class="block w-full rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder-slate-600
                                          focus:border-amber-500/70 focus:ring-2 focus:ring-amber-500/15 focus:outline-none transition" />
                            @error('phone')
                                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Admin Secret --}}
                    <div>
                        <label for="admin_secret" class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">
                            Admin Invite Code
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <svg class="h-4 w-4 text-amber-500/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                            <input id="admin_secret" name="admin_secret" type="password"
                                   required autocomplete="off"
                                   placeholder="Enter your invite code"
                                   class="block w-full rounded-xl border border-slate-800 bg-slate-900/80 pl-11 pr-4 py-3 text-sm text-white placeholder-slate-600
                                          focus:border-amber-500/70 focus:ring-2 focus:ring-amber-500/15 focus:outline-none transition" />
                        </div>
                        @error('admin_secret')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Divider --}}
                    <div class="flex items-center gap-3 py-1">
                        <div class="h-px flex-1 bg-slate-800"></div>
                        <span class="text-[10px] uppercase tracking-widest text-slate-600">Set password</span>
                        <div class="h-px flex-1 bg-slate-800"></div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">Password</label>
                        <input id="password" name="password" type="password"
                               required autocomplete="new-password"
                               placeholder="Minimum 8 characters"
                               class="block w-full rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-amber-500/70 focus:ring-2 focus:ring-amber-500/15 focus:outline-none transition" />
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password"
                               required autocomplete="new-password"
                               placeholder="Repeat your password"
                               class="block w-full rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-amber-500/70 focus:ring-2 focus:ring-amber-500/15 focus:outline-none transition" />
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full rounded-xl bg-amber-600 px-5 py-3.5 text-sm font-bold text-white
                                   hover:bg-amber-500 active:scale-[0.98]
                                   focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-[#080c12]
                                   transition-all duration-200 shadow-[0_8px_24px_-8px_rgba(245,158,11,0.45)]">
                        Request Admin Access
                    </button>
                </form>

                {{-- Footer links --}}
                <div class="mt-8 border-t border-slate-800/60 pt-6 text-center">
                    <p class="text-sm text-slate-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-amber-400 hover:text-amber-300 transition-colors">Sign in</a>
                    </p>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
