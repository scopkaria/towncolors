<x-public-layout title="Experiences">

{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  HERO                                                           ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="relative overflow-hidden bg-warm-100 border-b border-warm-300/40 py-16 sm:py-24">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-brand-primary/8 blur-[80px]"></div>
        <div class="absolute -bottom-16 right-0 h-56 w-56 rounded-full bg-accent-light blur-[60px]"></div>
    </div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <span class="reveal inline-flex rounded-full border border-accent/30 bg-accent-light px-3.5 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-primary sm:px-4 sm:py-1.5 sm:text-[11px] sm:tracking-[0.3em]">
                Explore
            </span>
            <h1 class="reveal reveal-delay-1 mt-5 font-display text-[1.75rem] font-bold leading-[1.15] text-brand-ink sm:mt-6 sm:text-4xl lg:text-5xl">
                Our Experiences
            </h1>
            <p class="reveal reveal-delay-2 mt-4 text-[0.9375rem] leading-7 text-brand-muted sm:text-lg sm:leading-8">
                Discover the full range of experiences we offer — handcrafted tour types designed to inspire and deliver results.
            </p>
        </div>
    </div>
</section>

{{-- ╔══════════════════════════════════════════════════════════════════╗
     ║  EXPERIENCE CARDS GRID                                          ║
     ╚══════════════════════════════════════════════════════════════════╝ --}}
<section class="py-14 sm:py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">

        @if ($experiences->isEmpty())
            {{-- Empty state --}}
            <div class="reveal flex flex-col items-center justify-center py-24 text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl border border-warm-300/50 bg-warm-100 shadow-card">
                    <svg class="h-10 w-10 text-brand-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="mt-5 font-display text-xl text-brand-ink">No experiences yet</h2>
                <p class="mt-2 text-sm text-brand-muted">Check back soon — we're always crafting new experiences.</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($experiences as $index => $experience)
                    <a href="{{ route('services.show', $experience) }}"
                       class="reveal reveal-delay-{{ ($index % 3) + 1 }} group relative flex flex-col overflow-hidden rounded-3xl border border-warm-300/40 bg-warm-100 shadow-card transition duration-300 ease-out hover:-translate-y-1 hover:border-brand-primary/25 hover:shadow-[0_28px_55px_-20px_rgba(249,115,22,0.18)]">

                        {{-- Image / placeholder --}}
                        <div class="relative h-52 shrink-0 overflow-hidden sm:h-56">
                            @if ($experience->image_path)
                                <img src="{{ asset('storage/' . $experience->image_path) }}"
                                     alt="{{ $experience->name }}"
                                     class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                     loading="lazy">
                            @else
                                {{-- Gradient placeholder --}}
                                <div class="flex h-full w-full items-center justify-center"
                                     style="background: linear-gradient(135deg, {{ $experience->color ?? '#FFB162' }}22 0%, {{ $experience->color ?? '#FFB162' }}0a 100%);">
                                    <svg class="h-16 w-16 opacity-20" fill="none" viewBox="0 0 24 24"
                                         stroke="{{ $experience->color ?? '#FFB162' }}" stroke-width="1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Colour accent bar on hover --}}
                            <div class="absolute bottom-0 left-0 right-0 h-1 scale-x-0 rounded-t-sm transition duration-300 group-hover:scale-x-100"
                                 style="background-color: {{ $experience->color ?? 'var(--primary-color)' }};"></div>

                            {{-- Projects badge --}}
                            @if ($experience->projects_count > 0)
                                <div class="absolute right-3 top-3 flex items-center gap-1.5 rounded-full border border-white/80 bg-white/90 px-2.5 py-1 text-xs font-semibold text-brand-ink shadow-sm backdrop-blur-sm">
                                    <span class="h-1.5 w-1.5 rounded-full bg-brand-primary"></span>
                                    {{ $experience->projects_count }} {{ Str::plural('project', $experience->projects_count) }}
                                </div>
                            @endif
                        </div>

                        {{-- Card body --}}
                        <div class="flex flex-1 flex-col gap-3 p-6">

                            {{-- Title + colour dot --}}
                            <div class="flex items-start gap-3">
                                <span class="mt-1 h-3 w-3 shrink-0 rounded-full"
                                      style="background-color: {{ $experience->color ?? '#FFB162' }};"></span>
                                <h2 class="font-display text-lg font-bold leading-snug text-brand-ink transition duration-200 group-hover:text-brand-primary">
                                    {{ $experience->name }}
                                </h2>
                            </div>

                            {{-- Description --}}
                            @if ($experience->description)
                                <p class="line-clamp-3 text-sm leading-6 text-brand-muted">
                                    {{ $experience->description }}
                                </p>
                            @endif

                            {{-- Meta row --}}
                            <div class="mt-auto flex flex-wrap items-center gap-3 pt-2">
                                @if ($experience->price_range)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-warm-300/50 bg-warm-200/50 px-3 py-1 text-xs font-semibold text-brand-muted">
                                        <svg class="h-3 w-3 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $experience->price_range }}
                                    </span>
                                @endif

                                @if ($experience->estimated_duration)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-warm-300/50 bg-warm-200/50 px-3 py-1 text-xs font-semibold text-brand-muted">
                                        <svg class="h-3 w-3 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $experience->estimated_duration }}
                                    </span>
                                @endif

                                <span class="ml-auto flex items-center gap-1 text-xs font-semibold text-brand-primary opacity-0 transition duration-200 group-hover:opacity-100">
                                    Explore
                                    <svg class="h-3.5 w-3.5 translate-x-0 transition duration-200 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Bottom CTA --}}
            <div class="reveal mt-14 text-center">
                <p class="text-sm text-brand-muted">Ready to get started?</p>
                <a href="{{ route('register.client') }}"
                   class="btn-primary mt-4 inline-flex">
                    Book an Experience
                </a>
            </div>
        @endif
    </div>
</section>

</x-public-layout>
