<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Sign In â€” Towncore</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center p-4 lg:p-0" style="background:#080600;">

    <div class="w-full h-full lg:min-h-screen flex flex-col lg:flex-row max-w-[1200px] mx-auto lg:max-w-none lg:fixed lg:inset-0">

        {{-- â”€â”€ Left decorative panel â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <aside class="relative hidden lg:flex lg:w-[46%] xl:w-[44%] flex-col justify-between overflow-hidden px-12 py-12"
               style="background:linear-gradient(145deg,#0d0a02 0%,#140f02 100%);">

            <div class="pointer-events-none absolute inset-0"
                 style="background-image:linear-gradient(rgba(217,119,6,0.08) 1px,transparent 1px),
                                         linear-gradient(90deg,rgba(217,119,6,0.08) 1px,transparent 1px);
                        background-size:36px 36px;"></div>
            <div class="pointer-events-none absolute -bottom-32 -left-16 h-[480px] w-[480px] rounded-full blur-[110px]"
                 style="background:rgba(217,119,6,0.18);"></div>
            <div class="pointer-events-none absolute top-0 right-0 h-[250px] w-[250px] rounded-full blur-[90px]"
                 style="background:rgba(245,158,11,0.07);"></div>

            {{-- Corner frame accents --}}
            <div class="absolute top-0 left-0 h-[2px] w-40 bg-gradient-to-r from-amber-600/60 to-transparent"></div>
            <div class="absolute top-0 left-0 h-40 w-[2px] bg-gradient-to-b from-amber-600/60 to-transparent"></div>
            <div class="absolute bottom-0 right-0 h-[2px] w-40 bg-gradient-to-l from-amber-600/30 to-transparent"></div>
            <div class="absolute bottom-0 right-0 h-40 w-[2px] bg-gradient-to-t from-amber-600/30 to-transparent"></div>

            {{-- Brand --}}
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl border border-amber-500/25 bg-amber-500/10">
                        <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="font-display text-lg font-bold text-white tracking-tight">Towncore</span>
                </a>
            </div>

            {{-- Main copy --}}
            <div class="relative z-10 space-y-6 my-auto py-14">
                <span class="inline-block rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.3em] text-amber-300">
                    Super Admin Â· Restricted
                </span>
                <h1 class="font-display text-4xl xl:text-[2.75rem] font-bold leading-[1.15] text-white">
                    Platform<br>
                    <span class="bg-gradient-to-r from-amber-300 via-yellow-300 to-amber-400 bg-clip-text text-transparent">
                        command centre.
                    </span>
                </h1>
                <p class="text-[0.9rem] leading-7 text-stone-400 max-w-sm">
                    Full platform control â€” users, projects, analytics, billing, and system configuration. Authorised access only.
                </p>

                <div class="space-y-3.5 pt-3">
                    @foreach ([
                        'User & role management',
                        'Platform analytics & reporting',
                        'Billing, fees & payouts',
                        'System settings & configuration',
                    ] as $f)
                    <div class="flex items-center gap-3">
                        <div class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500/15 border border-amber-500/20">
                            <svg class="h-2.5 w-2.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm text-stone-400">{{ $f }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Security notice --}}
                <div class="rounded-xl border border-amber-600/15 bg-amber-600/5 p-4 mt-2">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-xs leading-5 text-amber-200/60">
                            This portal is for authorised administrators only. All access attempts are logged and monitored.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Bottom decoration --}}
            <div class="relative z-10 flex items-center gap-4">
                <div class="h-px flex-1 bg-gradient-to-r from-amber-600/30 to-transparent"></div>
                <span class="text-[10px] uppercase tracking-[0.3em] text-amber-600/40">Restricted portal</span>
                <div class="h-px flex-1 bg-gradient-to-l from-amber-600/30 to-transparent"></div>
            </div>
        </aside>

        {{-- â”€â”€ Right panel / Form â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <main class="flex flex-1 flex-col items-center justify-center overflow-y-auto px-5 py-12 sm:px-8 lg:px-14 xl:px-20"
              style="background:#080600;">

            {{-- Mobile logo (no link back to chooser - admin URL is private) --}}
            <div class="mb-8 flex items-center gap-3 lg:hidden">
                <div class="h-9 w-9 flex items-center justify-center rounded-xl border border-amber-500/20 bg-amber-500/10">
                    <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="font-display text-lg font-bold text-white">Towncore</span>
            </div>

            <div class="w-full max-w-md animate-[page-fade_0.55s_ease-out_both]">

                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-amber-500/20 bg-amber-500/8 px-4 py-3 text-sm text-amber-300">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Heading --}}
                <div class="mb-10">
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-amber-500/25 bg-amber-500/8 px-3 py-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.25em] text-amber-300">Super Admin</span>
                    </div>
                    <h2 class="font-display text-3xl font-bold text-white mb-2">Welcome back</h2>
                    <p class="text-sm text-stone-500">Login to continue</p>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('login.admin.post') }}" class="space-y-5" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="block text-[11px] font-semibold uppercase tracking-[0.16em] text-stone-500 mb-2">Email Address</label>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}"
                               required autofocus autocomplete="username"
                               placeholder="admin@towncore.io"
                               class="block w-full rounded-xl border border-stone-800/80 bg-[#100d02] px-4 py-3 text-sm text-white placeholder-stone-700
                                      focus:border-amber-600/50 focus:ring-2 focus:ring-amber-500/15 focus:outline-none transition" />
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-[11px] font-semibold uppercase tracking-[0.16em] text-stone-500">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-amber-500 hover:text-amber-400 transition-colors">Forgot password?</a>
                            @endif
                        </div>
                        <input id="password" name="password" type="password"
                               required autocomplete="current-password"
                               placeholder="Your password"
                               class="block w-full rounded-xl border border-stone-800/80 bg-[#100d02] px-4 py-3 text-sm text-white placeholder-stone-700
                                      focus:border-amber-600/50 focus:ring-2 focus:ring-amber-500/15 focus:outline-none transition" />
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input name="remember" type="checkbox"
                               class="h-4 w-4 rounded border-stone-700 bg-[#100d02] text-amber-500 focus:ring-amber-500/20 focus:ring-offset-0" />
                        <span class="text-sm text-stone-500">Keep me signed in</span>
                    </label>

                    <button type="submit"
                            class="w-full rounded-xl px-5 py-4 text-sm font-bold text-white transition-all duration-200 active:scale-[0.98]
                                   focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                            style="background:linear-gradient(135deg,#b45309,#d97706);
                                   box-shadow:0 8px 28px -8px rgba(217,119,6,0.50);">
                        Sign In
                    </button>
                </form>

                {{-- Footer --}}
                <div class="mt-10 pt-6 border-t border-white/5">
                    <p class="text-center text-xs text-stone-700">
                        Access is provisioned by the platform owner.
                    </p>
                    <p class="text-center mt-3">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs text-stone-600 hover:text-stone-400 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            View Website
                        </a>
                    </p>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
