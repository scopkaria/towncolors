<x-public-layout title="Services">

{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  HERO                                                           ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="relative overflow-hidden bg-white py-16 sm:py-24 border-b border-stone-100">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-primary/8 blur-[80px]"></div>
        <div class="absolute -bottom-16 right-0 h-56 w-56 rounded-full bg-orange-50 blur-[60px]"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="reveal inline-flex rounded-full border border-orange-200 bg-orange-50 px-3.5 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-primary sm:px-4 sm:py-1.5 sm:text-[11px] sm:tracking-[0.3em]">
                What We Offer
            </span>
            <h1 class="reveal reveal-delay-1 mt-5 font-display text-[1.75rem] font-bold leading-[1.15] text-brand-ink sm:mt-6 sm:text-4xl lg:text-5xl">
                Our Services
            </h1>
            <p class="reveal reveal-delay-2 mt-4 text-[0.9375rem] leading-7 text-brand-muted sm:text-lg sm:leading-8">
                Browse our full range of digital services — from design to development, we craft solutions that perform.
            </p>
        </div>
    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  CATEGORIES GRID                                                ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="py-14 sm:py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">

        @if ($categories->isEmpty())
            {{-- Empty state --}}
            <div class="reveal flex flex-col items-center justify-center py-24 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-stone-200 bg-white shadow-card">
                    <svg class="h-10 w-10 text-brand-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="mt-5 font-display text-xl text-brand-ink">No services yet</h2>
                <p class="mt-2 text-sm text-brand-muted">Check back soon — we're always adding new capabilities.</p>
            </div>
        @else
{{-- Grid --}}
            <div class="grid gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                @foreach ($categories as $index => $category)
                    <a href="{{ route('services.show', $category) }}"
                       class="reveal reveal-delay-{{ ($index % 3) + 1 }} group flex flex-col overflow-hidden rounded-2xl border border-white/70 bg-white/90 shadow-card backdrop-blur-sm transition duration-300 ease-out hover:-translate-y-1 hover:border-brand-primary/25 hover:shadow-[0_28px_55px_-20px_rgba(249,115,22,0.18)]
                              dark:border-slate-700/50 dark:bg-slate-800/80 dark:hover:border-orange-500/40
                              sm:rounded-3xl">

                        {{-- Thumbnail --}}
                        <div class="relative h-48 shrink-0 overflow-hidden sm:h-52">
                            @if ($category->image_path)
                                <img src="{{ asset('storage/' . $category->image_path) }}"
                                     alt="{{ $category->name }}"
                                     class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                     loading="lazy">
                            @else
                                {{-- Branded placeholder --}}
                                <div class="flex h-full w-full items-center justify-center"
                                     style="background: linear-gradient(135deg, {{ $category->color ?? '#f97316' }}18 0%, {{ $category->color ?? '#f97316' }}08 100%);">
                                    <svg class="h-14 w-14 opacity-20" fill="none" viewBox="0 0 24 24" stroke="{{ $category->color ?? 'currentColor' }}" stroke-width="1.2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Color accent bar --}}
                            <div class="absolute bottom-0 left-0 right-0 h-0.5 opacity-0 transition duration-300 group-hover:opacity-100"
                                 style="background-color: {{ $category->color ?? 'var(--primary-color)' }};"></div>
                        </div>

                        {{-- Content --}}
                        <div class="flex flex-1 flex-col p-5 sm:p-6">

                            {{-- Title row --}}
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="font-display text-[1.0625rem] text-brand-ink transition duration-200 group-hover:text-brand-primary sm:text-lg">
                                    {{ $category->name }}
                                </h2>

                                @if ($category->projects_count > 0)
                                    <span class="mt-0.5 shrink-0 inline-flex items-center rounded-full border border-stone-200 bg-stone-50 px-2 py-0.5 text-[10px] font-semibold text-brand-muted">
                                        {{ $category->projects_count }} {{ Str::plural('project', $category->projects_count) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Description --}}
                            <p class="mt-2 flex-1 text-sm leading-6 text-brand-muted line-clamp-3 sm:leading-7">
                                {{ $category->description ?: 'Professional ' . $category->name . ' solutions crafted to meet your unique business needs.' }}
                            </p>

                            {{-- Metadata row --}}
                            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-stone-100 pt-4 dark:border-slate-700/50">
                                @if ($category->price_range)
                                    <span class="flex items-center gap-1.5 text-xs text-brand-muted">
                                        <svg class="h-3.5 w-3.5 shrink-0 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $category->price_range }}
                                    </span>
                                @endif

                                @if ($category->estimated_duration)
                                    <span class="flex items-center gap-1.5 text-xs text-brand-muted">
                                        <svg class="h-3.5 w-3.5 shrink-0 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $category->estimated_duration }}
                                    </span>
                                @endif

                                {{-- Always-visible CTA --}}
                                <span class="ml-auto flex items-center gap-1 text-xs font-semibold text-brand-primary opacity-0 transition duration-200 group-hover:opacity-100">
                                    Explore
                                    <svg class="h-3.5 w-3.5 transition duration-300 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </div>
</section>


{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  CTA STRIP                                                      ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="border-t border-stone-100 bg-white/60 py-12 dark:border-slate-700/50 dark:bg-slate-900/60 sm:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        <div class="reveal flex flex-col items-center gap-5 text-center sm:flex-row sm:justify-between sm:text-left">
            <div>
                <h2 class="font-display text-xl text-brand-ink sm:text-2xl">
                    Not sure where to start?
                </h2>
                <p class="mt-1.5 text-sm text-brand-muted sm:mt-2 sm:text-base">
                    Tell us about your project and we'll recommend the right service.
                </p>
            </div>
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                <a href="{{ url('/client/projects/create') }}" class="btn-primary">
                    <svg class="mr-2 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Start a Project
                </a>
                <a href="{{ url('/page/contact') }}" class="btn-secondary">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</section>

</x-public-layout>
