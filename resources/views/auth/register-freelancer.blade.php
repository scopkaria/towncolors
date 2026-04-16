<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Freelancer Registration — Towncore</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-navy-800 min-h-screen flex items-center justify-center p-4 lg:p-0">

    <div class="w-full h-full lg:min-h-screen flex flex-col lg:flex-row-reverse max-w-[1200px] mx-auto lg:max-w-none lg:fixed lg:inset-0">

        {{-- ── Right panel (decorative — appears on left for freelancer via flex-row-reverse) --}}
        <aside class="relative hidden lg:flex lg:w-[46%] xl:w-[42%] flex-col justify-between overflow-hidden bg-[#0f0d1e] px-12 py-12">

            {{-- Hex mesh texture --}}
            <div class="pointer-events-none absolute inset-0"
                 style="background-image:radial-gradient(rgba(139,92,246,0.14) 1px,transparent 1px);background-size:28px 28px;"></div>

            {{-- Violet glow blobs --}}
            <div class="pointer-events-none absolute -top-40 -right-20 h-[480px] w-[480px] rounded-full bg-violet-600/22 blur-[110px]"></div>
            <div class="pointer-events-none absolute bottom-0 left-0 h-[260px] w-[260px] rounded-full bg-violet-400/8 blur-[80px]"></div>

            {{-- Top brand --}}
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-violet-500/20 border border-violet-500/30">
                        <svg class="h-5 w-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-display text-lg font-bold text-white tracking-tight">Towncore</span>
                </a>
            </div>

            {{-- Main copy --}}
            <div class="relative z-10 space-y-8 my-auto py-16">
                <div>
                    <span class="inline-block rounded-full border border-violet-500/30 bg-violet-500/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.3em] text-violet-300">
                        For Freelancers
                    </span>
                </div>
                <h1 class="font-display text-4xl xl:text-5xl font-bold leading-tight text-white">
                    Turn your skills<br>
                    <span class="bg-gradient-to-r from-violet-300 to-violet-500 bg-clip-text text-transparent">into a career.</span>
                </h1>
                <p class="text-base leading-8 text-slate-400 max-w-sm">
                    Build your reputation, win quality projects, and receive secure payments — without chasing clients or sending another invoice email.
                </p>

                {{-- Feature list --}}
                <div class="space-y-4 pt-2">
                    @foreach ([
                        ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'text' => 'Showcase your work in a live portfolio'],
                        ['icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'text' => 'Bid on projects that match your skills'],
                        ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Automated invoices & secure payouts'],
                        ['icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'text' => 'Build 5-star reviews & grow your rate'],
                    ] as $feature)
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-violet-500/20 border border-violet-500/20">
                            <svg class="h-3.5 w-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-sm leading-7 text-slate-300">{{ $feature['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom stats --}}
            <div class="relative z-10 grid grid-cols-3 gap-3">
                @foreach ([['$4.2M','Paid out to date'],['850+','Active freelancers'],['4.9★','Platform rating']] as $stat)
                <div class="rounded-xl border border-white/8 bg-white/5 px-4 py-4 text-center">
                    <p class="font-display text-xl font-bold text-violet-300">{{ $stat[0] }}</p>
                    <p class="mt-0.5 text-[10px] uppercase tracking-widest text-slate-500">{{ $stat[1] }}</p>
                </div>
                @endforeach
            </div>
        </aside>

        {{-- ── Left panel / Form ──────────────────────────────── --}}
        <main class="flex flex-1 flex-col items-center justify-center overflow-y-auto bg-navy-800 px-5 py-12 sm:px-8 lg:px-14 xl:px-20">

            {{-- Mobile logo --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <div class="h-9 w-9 flex items-center justify-center rounded-xl bg-violet-500/20 border border-violet-500/25">
                    <svg class="h-5 w-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="font-display text-lg font-bold text-white">Towncore</span>
            </div>

            <div class="w-full max-w-md animate-[page-fade_0.55s_ease-out_both]">

                {{-- Breadcrumb --}}
                <div class="mb-8">
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-300 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Choose a different role
                    </a>
                </div>

                {{-- Heading --}}
                <div class="mb-8 space-y-2">
                    <div class="inline-flex items-center gap-2 rounded-full border border-violet-500/30 bg-violet-500/10 px-3 py-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-violet-400"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-violet-300">Freelancer account</span>
                    </div>
                    <h2 class="font-display text-3xl font-bold text-white">Join as a freelancer</h2>
                    <p class="text-sm text-slate-400 leading-6">Set up your profile and start bidding on projects in minutes.</p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('register.freelancer') }}" class="space-y-5" novalidate>
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">Full Name</label>
                        <input id="name" name="name" type="text"
                               value="{{ old('name') }}"
                               required autofocus autocomplete="name"
                               placeholder="Alex Johnson"
                               class="block w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:outline-none transition" />
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
                               placeholder="alex@portfolio.dev"
                               class="block w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:outline-none transition" />
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
                                   placeholder="alex_dev"
                                   class="block w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder-slate-600
                                          focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:outline-none transition" />
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
                                   class="block w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder-slate-600
                                          focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:outline-none transition" />
                            @error('phone')
                                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-[0.15em] text-slate-400 mb-2">Password</label>
                        <input id="password" name="password" type="password"
                               required autocomplete="new-password"
                               placeholder="Minimum 8 characters"
                               class="block w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:outline-none transition" />
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
                               class="block w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-violet-500 focus:ring-2 focus:ring-violet-500/20 focus:outline-none transition" />
                    </div>

                    {{-- Terms text --}}
                    <p class="text-xs text-slate-600 leading-5">
                        By registering you agree to Towncore's
                        <span class="text-violet-400">Terms of Service</span> and <span class="text-violet-400">Privacy Policy</span>.
                    </p>

                    <label class="flex items-start gap-3 rounded-xl border border-slate-700/70 bg-slate-900/60 px-4 py-3">
                        <input type="checkbox" name="start_free_trial" value="1" {{ old('start_free_trial') ? 'checked' : '' }} class="mt-0.5 rounded border-slate-600 bg-slate-900 text-violet-500 focus:ring-violet-500/30">
                        <span>
                            <span class="block text-sm font-semibold text-white">Start Free Trial (5 Days)</span>
                            <span class="block text-xs text-slate-400 mt-1">Optional and manual. If not activated, your account stays in limited mode.</span>
                        </span>
                    </label>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full rounded-xl bg-violet-600 px-5 py-3.5 text-sm font-bold text-white
                                   hover:bg-violet-500 active:scale-[0.98]
                                   focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-navy-800
                                   transition-all duration-200 shadow-[0_8px_24px_-8px_rgba(139,92,246,0.5)]">
                        Create Freelancer Account
                    </button>
                </form>

                {{-- Footer links --}}
                <div class="mt-8 flex items-center justify-between text-sm">
                    <p class="text-slate-500">
                        Already registered?
                        <a href="{{ route('login') }}" class="font-semibold text-violet-400 hover:text-violet-300 transition-colors">Sign in</a>
                    </p>
                    <a href="{{ route('register.client') }}" class="text-slate-600 hover:text-slate-400 transition-colors text-xs">
                        I'm a client →
                    </a>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
