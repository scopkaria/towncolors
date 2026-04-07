<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Freelancer Sign In â€” Towncore</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 lg:p-0" style="background:#060d09;">

    <div class="w-full h-full lg:min-h-screen flex flex-col lg:flex-row-reverse max-w-[1200px] mx-auto lg:max-w-none lg:fixed lg:inset-0">

        {{-- â”€â”€ Decorative panel (sits on the right via flex-row-reverse) â”€â”€ --}}
        <aside class="relative hidden lg:flex lg:w-[46%] xl:w-[44%] flex-col justify-between overflow-hidden px-12 py-12"
               style="background:linear-gradient(145deg,#071209 0%,#061a0e 100%);">

            <div class="pointer-events-none absolute inset-0"
                 style="background-image:radial-gradient(rgba(16,185,129,0.12) 1px,transparent 1px);background-size:28px 28px;"></div>
            <div class="pointer-events-none absolute -top-32 -right-16 h-[440px] w-[440px] rounded-full blur-[110px]"
                 style="background:rgba(16,185,129,0.18);"></div>
            <div class="pointer-events-none absolute bottom-0 left-0 h-[240px] w-[240px] rounded-full blur-[80px]"
                 style="background:rgba(5,150,105,0.10);"></div>

            <div class="absolute top-0 right-0 h-[2px] w-48 bg-gradient-to-l from-emerald-500/50 to-transparent"></div>
            <div class="absolute bottom-0 left-0 h-[2px] w-48 bg-gradient-to-r from-emerald-500/30 to-transparent"></div>

            {{-- Brand --}}
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl border border-emerald-400/20 bg-emerald-500/15">
                        <svg class="h-5 w-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="font-display text-lg font-bold text-white tracking-tight">Towncore</span>
                </a>
            </div>

            {{-- Main copy --}}
            <div class="relative z-10 space-y-6 my-auto py-14">
                <span class="inline-block rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-300">
                    Freelancer Portal
                </span>
                <h1 class="font-display text-4xl xl:text-[2.75rem] font-bold leading-[1.15] text-white">
                    Back to work,<br>
                    <span class="bg-gradient-to-r from-emerald-300 via-teal-300 to-emerald-400 bg-clip-text text-transparent">
                        effortlessly.
                    </span>
                </h1>
                <p class="text-[0.9rem] leading-7 text-slate-400 max-w-sm">
                    Resume active projects, respond to new briefs, collect your earnings â€” your workspace is exactly as you left it.
                </p>

                <div class="space-y-3.5 pt-3">
                    @foreach ([
                        'Active projects & proposals',
                        'Earnings tracker & invoices',
                        'Client messaging & file sharing',
                        'Portfolio & profile management',
                    ] as $f)
                    <div class="flex items-center gap-3">
                        <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 border border-emerald-500/20">
                            <svg class="h-2.5 w-2.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm text-slate-400">{{ $f }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom stats --}}
            <div class="relative z-10 grid grid-cols-3 gap-3">
                @foreach ([['$4.2M','Paid out'],['850+','Freelancers'],['4.9 â˜…','Rating']] as $s)
                <div class="rounded-xl border border-white/6 bg-white/4 px-3 py-4 text-center">
                    <p class="font-display text-lg font-bold text-emerald-300">{{ $s[0] }}</p>
                    <p class="mt-1 text-[10px] uppercase tracking-widest text-slate-600">{{ $s[1] }}</p>
                </div>
                @endforeach
            </div>
        </aside>

        {{-- â”€â”€ Left panel / Form â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <main class="flex flex-1 flex-col items-center justify-center overflow-y-auto px-5 py-12 sm:px-8 lg:px-14 xl:px-20"
              style="background:#060d09;">

            {{-- Mobile logo --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <div class="h-9 w-9 flex items-center justify-center rounded-xl border border-emerald-500/20 bg-emerald-500/10">
                    <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="font-display text-lg font-bold text-white">Towncore</span>
            </div>

            <div class="w-full max-w-md animate-[page-fade_0.55s_ease-out_both]">

                {{-- Back link --}}
                <div class="mb-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-600 hover:text-slate-300 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        View Website
                    </a>
                </div>

                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-500/20 bg-emerald-500/8 px-4 py-3 text-sm text-emerald-300">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Heading --}}
                <div class="mb-10">
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-emerald-500/25 bg-emerald-500/8 px-3 py-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-emerald-300">Freelancer account</span>
                    </div>
                    <h2 class="font-display text-3xl font-bold text-white mb-2">Welcome back</h2>
                    <p class="text-sm text-slate-500">Login to continue</p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('login.freelancer.post') }}" class="space-y-6" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="block text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 mb-2">Email Address</label>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}"
                               required autofocus autocomplete="username"
                               placeholder="alex@portfolio.dev"
                               class="block w-full rounded-xl border border-slate-800 bg-[#0b1510] px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition" />
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-emerald-400 hover:text-emerald-300 transition-colors">Forgot password?</a>
                            @endif
                        </div>
                        <input id="password" name="password" type="password"
                               required autocomplete="current-password"
                               placeholder="Your password"
                               class="block w-full rounded-xl border border-slate-800 bg-[#0b1510] px-4 py-3 text-sm text-white placeholder-slate-600
                                      focus:border-emerald-500/50 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition" />
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input name="remember" type="checkbox"
                               class="h-4 w-4 rounded border-slate-700 bg-[#0b1510] text-emerald-500 focus:ring-emerald-500/20 focus:ring-offset-0" />
                        <span class="text-sm text-slate-500">Keep me signed in</span>
                    </label>

                    <button type="submit"
                            class="w-full rounded-xl px-5 py-4 text-sm font-bold text-white transition-all duration-200 active:scale-[0.98]
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                            style="background:linear-gradient(135deg,#059669,#10b981);
                                   box-shadow:0 8px 28px -8px rgba(16,185,129,0.45);">
                        Sign In
                    </button>
                </form>

                {{-- Footer --}}
                <div class="mt-10 space-y-4 pt-6 border-t border-white/5">
                    <p class="text-sm text-slate-600 text-center">
                        No account?
                        <a href="{{ route('register.freelancer') }}" class="font-semibold text-emerald-400 hover:text-emerald-300 transition-colors">Create one</a>
                    </p>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
