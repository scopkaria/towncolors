<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO --}}
    <title>{{ $category->name }} · {{ config('app.name', 'Towncore') }}</title>
    <meta name="description" content="{{ $category->description ?? 'Explore ' . $category->name . ' projects delivered by our talented team on ' . config('app.name') . '.' }}">
    <link rel="canonical" href="{{ route('services.show', $category) }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $category->name }} · {{ config('app.name') }}">
    <meta property="og:description" content="{{ $category->description ?? 'Explore ' . $category->name . ' projects.' }}">
    @if ($category->featured_image)
        <meta property="og:image" content="{{ asset('storage/' . $category->featured_image) }}">
    @endif
    <meta property="og:url" content="{{ route('services.show', $category) }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />
    {{-- Dark mode anti-flash --}}
    <script>(function(){try{var s=localStorage.getItem('darkMode');var sys=window.matchMedia('(prefers-color-scheme: dark)').matches;if(s==='true'||(s===null&&sys)){document.documentElement.classList.add('dark');}}catch(e){}})();</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.theme-vars')
</head>
<body class="font-sans antialiased transition-colors duration-300">

    <div class="min-h-screen bg-gradient-to-br from-stone-50 via-white to-orange-50/30 transition-colors duration-300 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

        {{-- ── Nav ── --}}
        <header class="sticky top-0 z-30 border-b border-white/70 bg-white/80 backdrop-blur-xl transition-colors duration-300
                       dark:border-slate-700/50 dark:bg-slate-900/85"
                x-data="{
                    isDark: document.documentElement.classList.contains('dark'),
                    toggleDark() {
                        this.isDark = !this.isDark;
                        document.documentElement.classList.toggle('dark', this.isDark);
                        localStorage.setItem('darkMode', this.isDark.toString());
                    }
                }">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <x-site-logo
                        icon-wrap-class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-950 text-white shadow-card"
                        icon-class="h-6 w-6"
                        name-class="font-display text-xl text-brand-ink"
                        logo-class="h-10 w-auto object-contain"
                    />
                </a>

                <nav class="flex items-center gap-3">
                    <a href="{{ route('portfolio.public') }}" class="hidden text-sm font-medium text-brand-muted transition hover:text-brand-primary sm:block">Portfolio</a>
                    {{-- Dark mode toggle --}}
                    <button type="button" @click="toggleDark()"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-stone-200 bg-white text-brand-muted transition hover:border-orange-300 hover:bg-orange-50 hover:text-brand-primary dark:border-slate-600 dark:bg-slate-800 dark:text-slate-400 dark:hover:border-orange-400 dark:hover:bg-orange-500/15 dark:hover:text-brand-primary"
                            :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'">
                        <svg x-show="isDark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364-.707-.707M6.343 6.343l-.707-.707m12.728 0-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!isDark" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    @auth
                        <a href="{{ route(auth()->user()->role->value . '.dashboard') }}" class="btn-secondary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                        <a href="{{ route('register') }}" class="btn-primary">Get started</a>
                    @endauth
                </nav>
            </div>
        </header>

        {{-- ── Hero ── --}}
        <section class="relative overflow-hidden">
            @if ($category->featured_image)
                {{-- Full-bleed image hero --}}
                <div class="relative h-72 sm:h-96 lg:h-[480px]">
                    <img src="{{ asset('storage/' . $category->featured_image) }}"
                         alt="{{ $category->name }}"
                         class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

                    <div class="relative flex h-full flex-col items-start justify-end px-5 pb-10 sm:px-8 lg:px-16">
                        <div class="mx-auto w-full max-w-7xl">
                            <span class="inline-flex rounded-full border border-white/40 bg-white/20 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-white backdrop-blur-sm">
                                Service
                            </span>
                            <h1 class="mt-4 font-display text-4xl leading-tight text-white drop-shadow-sm sm:text-5xl lg:text-6xl">
                                {{ $category->name }}
                            </h1>
                            @if ($category->description)
                                <p class="mt-4 max-w-2xl text-base leading-8 text-white/80">
                                    {{ $category->description }}
                                </p>
                            @endif

                            <div class="mt-6 flex flex-wrap items-center gap-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/20 px-4 py-2 text-sm font-semibold text-white backdrop-blur-sm">
                                    <span class="inline-block h-2 w-2 rounded-full" style="background-color: {{ $category->color }}"></span>
                                    {{ $projects->count() }} completed project{{ $projects->count() !== 1 ? 's' : '' }}
                                </span>
                                @auth
                                    @if (auth()->user()->role->value === 'client')
                                        <a href="{{ route('client.projects.create') }}"
                                           class="inline-flex items-center gap-2 rounded-full bg-brand-primary px-5 py-2 text-sm font-semibold text-white shadow-lg transition hover:bg-brand-hover">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                            Start a Project
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('register') }}"
                                       class="inline-flex items-center gap-2 rounded-full bg-brand-primary px-5 py-2 text-sm font-semibold text-white shadow-lg transition hover:bg-brand-hover">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                        Get started
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Text-only hero with colour accent --}}
                <div class="mx-auto max-w-7xl px-5 pb-10 pt-14 sm:px-8 sm:pt-20">
                    <div class="relative overflow-hidden rounded-3xl p-8 sm:p-12 lg:p-16"
                         style="background: linear-gradient(135deg, {{ $category->color }}18 0%, {{ $category->color }}08 100%); border: 1px solid {{ $category->color }}30">

                        {{-- Decorative blob --}}
                        <div class="pointer-events-none absolute -right-12 -top-12 h-64 w-64 rounded-full opacity-10"
                             style="background-color: {{ $category->color }}"></div>

                        <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em]"
                              style="border-color: {{ $category->color }}40; color: {{ $category->color }}">
                            @if ($category->image_path)
                                <img src="{{ asset('storage/' . $category->image_path) }}"
                                     alt="" class="h-4 w-4 rounded-full object-cover">
                            @endif
                            Service
                        </span>

                        <h1 class="mt-5 font-display text-4xl leading-tight text-brand-ink sm:text-5xl lg:text-6xl">
                            {{ $category->name }}
                        </h1>

                        @if ($category->description)
                            <p class="mt-5 max-w-2xl text-base leading-8 text-brand-muted">
                                {{ $category->description }}
                            </p>
                        @endif

                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            <span class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold"
                                  style="border-color: {{ $category->color }}40; color: {{ $category->color }}; background-color: {{ $category->color }}10">
                                {{ $projects->count() }} completed project{{ $projects->count() !== 1 ? 's' : '' }}
                            </span>
                            @auth
                                @if (auth()->user()->role->value === 'client')
                                    <a href="{{ route('client.projects.create') }}" class="btn-primary">
                                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                        Start a Project
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="btn-primary">
                                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                                    Get started
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endif
        </section>

        {{-- ── Long description ── --}}
        @if ($category->long_description)
            <section class="mx-auto max-w-7xl px-5 py-12 sm:px-8">
                <div class="mx-auto max-w-3xl">
                    <div class="prose prose-stone prose-sm sm:prose-base max-w-none leading-8 text-brand-muted">
                        {!! nl2br(e($category->long_description)) !!}
                    </div>
                </div>
            </section>
        @endif

        {{-- ── Pricing & duration ── --}}
        @if ($category->price_range || $category->estimated_duration)
            <section class="mx-auto max-w-7xl px-5 pb-8 sm:px-8">
                <div class="flex flex-wrap gap-4">
                    @if ($category->price_range)
                        <div class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-white px-5 py-4 shadow-sm">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                  style="background-color: {{ $category->color }}15">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                                     style="color: {{ $category->color }}">
                                    <path stroke-linecap="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-brand-muted">Price Range</p>
                                <p class="mt-0.5 text-sm font-bold text-brand-ink">{{ $category->price_range }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($category->estimated_duration)
                        <div class="flex items-center gap-3 rounded-2xl border border-stone-200 bg-white px-5 py-4 shadow-sm">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                                  style="background-color: {{ $category->color }}15">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                                     style="color: {{ $category->color }}">
                                    <path stroke-linecap="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-brand-muted">Estimated Duration</p>
                                <p class="mt-0.5 text-sm font-bold text-brand-ink">{{ $category->estimated_duration }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- ── Subcategories ── --}}
        @if ($category->children->isNotEmpty())
            <section class="mx-auto max-w-7xl px-5 pb-6 sm:px-8">
                <h2 class="mb-4 font-display text-2xl text-brand-ink">Specialisations</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($category->children as $child)
                        <span class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold"
                              style="border-color: {{ $child->color }}40; color: {{ $child->color }}; background-color: {{ $child->color }}12">
                            <span class="h-2 w-2 rounded-full inline-block" style="background-color: {{ $child->color }}"></span>
                            {{ $child->name }}
                        </span>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ── Completed projects / Portfolio grid ── --}}
        <section class="mx-auto max-w-7xl px-5 pb-16 sm:px-8">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="font-display text-3xl text-brand-ink">Completed Work</h2>
                    <p class="mt-2 text-sm text-brand-muted">Projects we've delivered in this category.</p>
                </div>
            </div>

            @if ($projects->isEmpty())
                <div class="rounded-3xl border border-white/70 bg-white/90 p-16 text-center shadow-panel">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl"
                         style="background-color: {{ $category->color }}15">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                             style="color: {{ $category->color }}">
                            <path stroke-linecap="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/>
                        </svg>
                    </div>
                    <h3 class="mt-5 font-display text-xl text-brand-ink">No completed projects yet</h3>
                    <p class="mt-2 text-sm text-brand-muted">Be the first — create a project in this category.</p>
                    @guest
                        <a href="{{ route('register') }}" class="btn-primary mt-6 inline-flex">Get started</a>
                    @endguest
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($projects as $project)
                        <div class="group rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card transition duration-300 hover:-translate-y-0.5 hover:shadow-panel">
                            {{-- Category tags --}}
                            @if ($project->categories->isNotEmpty())
                                <div class="mb-3 flex flex-wrap gap-1.5">
                                    @foreach ($project->categories as $cat)
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold text-white"
                                              style="background-color: {{ $cat->color }}">
                                            {{ $cat->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <h3 class="font-display text-xl text-brand-ink group-hover:text-brand-primary transition duration-200">
                                {{ $project->title }}
                            </h3>
                            <p class="mt-3 line-clamp-3 text-sm leading-7 text-brand-muted">
                                {{ $project->description }}
                            </p>

                            <div class="mt-5 flex items-center justify-between border-t border-stone-100 pt-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-600">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Completed
                                </span>
                                <span class="text-xs text-brand-muted">{{ $project->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ── Other services ── --}}
        @if ($relatedCategories->isNotEmpty())
            <section class="border-t border-stone-100 bg-white/60 py-14">
                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <h2 class="mb-8 font-display text-2xl text-brand-ink">Explore Other Services</h2>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($relatedCategories as $related)
                            <a href="{{ route('services.show', $related) }}"
                               class="group flex items-center gap-4 rounded-2xl border border-stone-100 bg-white p-4 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-orange-100 hover:shadow-card">
                                @if ($related->image_path)
                                    <img src="{{ asset('storage/' . $related->image_path) }}"
                                         alt="{{ $related->name }}"
                                         class="h-12 w-12 shrink-0 rounded-xl object-cover shadow-sm">
                                @else
                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl text-lg font-bold text-white shadow-sm"
                                          style="background-color: {{ $related->color }}">
                                        {{ strtoupper(substr($related->name, 0, 1)) }}
                                    </span>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-semibold text-brand-ink group-hover:text-brand-primary transition duration-200">
                                        {{ $related->name }}
                                    </p>
                                    @if ($related->description)
                                        <p class="mt-0.5 truncate text-xs text-brand-muted">{{ $related->description }}</p>
                                    @endif
                                    <p class="mt-1 text-[10px] font-semibold" style="color: {{ $related->color }}">
                                        {{ $related->projects_count }} project{{ $related->projects_count !== 1 ? 's' : '' }}
                                    </p>
                                </div>

                                <svg class="h-4 w-4 shrink-0 text-stone-300 transition group-hover:text-brand-primary group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- ── Footer CTA ── --}}
        <section class="border-t border-stone-100 py-16">
            <div class="mx-auto max-w-3xl px-5 text-center sm:px-8">
                <span class="inline-flex rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    Ready to get started?
                </span>
                <h2 class="mt-5 font-display text-4xl text-brand-ink">
                    Let's build something together
                </h2>
                <p class="mx-auto mt-5 max-w-md text-base leading-8 text-brand-muted">
                    Post your {{ $category->name }} project today and get matched with a skilled freelancer.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    @auth
                        @if (auth()->user()->role->value === 'client')
                            <a href="{{ route('client.projects.create') }}" class="btn-primary">Create a Project</a>
                        @else
                            <a href="{{ route(auth()->user()->role->value . '.dashboard') }}" class="btn-secondary">Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn-primary">Create an account</a>
                        <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                    @endauth
                </div>
            </div>
        </section>

    </div>

</body>
</html>
