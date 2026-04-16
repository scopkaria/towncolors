<x-public-layout title="About Town Colors">

    @php $settings = \App\Models\Setting::first(); @endphp

    @push('head')
    <meta name="description" content="Learn about Town Colors, a technology-driven company founded in 2021 that transforms ideas into powerful digital solutions.">
    <link rel="canonical" href="{{ route('about') }}">
    @endpush

    <x-public-hero
        badge="About Town Colors"
        title="Product builders, operators, and creatives in one execution team"
        :subtitle="$settings?->heroSubtitle('about') ?: 'Town Colors is a technology company founded in 2021 by Geoffrey Karia (Scop Karia) and Lisa Steven. We combine software engineering, cloud operations, and brand storytelling so clients can launch faster and scale with less friction.'"
        :media="$settings?->heroMediaUrl('about')"
    />

    <section class="py-16 sm:py-24">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-8 lg:grid-cols-2 lg:items-center">
            <div class="reveal">
                <h2 class="font-display text-2xl font-bold text-brand-ink sm:text-4xl">How we work</h2>
                <p class="mt-5 text-sm leading-8 text-brand-muted sm:text-base">
                    Our delivery model blends discovery, architecture, design, implementation, and measurable post-launch support.
                    This keeps strategy and execution connected from the first workshop to production rollout.
                </p>
                <p class="mt-4 text-sm leading-8 text-brand-muted sm:text-base">
                    We serve founders, SMEs, and institutions that need dependable systems: custom business platforms, websites, cloud infrastructure,
                    and media assets aligned to real business outcomes.
                </p>
            </div>

            <div class="reveal reveal-delay-1 rounded-3xl border border-warm-300/50 bg-warm-100 p-7 shadow-card sm:p-9 dark:border-slate-700/50 dark:bg-navy-800">
                <h3 class="font-display text-xl font-semibold text-brand-ink">Founders</h3>
                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-warm-300/40 bg-warm-200/50 p-4 dark:border-slate-700/40 dark:bg-navy-800/50">
                        <p class="text-xs uppercase tracking-[0.2em] text-brand-muted">Co-Founder</p>
                        <p class="mt-2 font-display text-lg text-brand-ink">Geoffrey Karia</p>
                        <p class="text-sm text-brand-muted">Scop Karia</p>
                    </div>
                    <div class="rounded-2xl border border-warm-300/40 bg-warm-200/50 p-4 dark:border-slate-700/40 dark:bg-navy-800/50">
                        <p class="text-xs uppercase tracking-[0.2em] text-brand-muted">Co-Founder</p>
                        <p class="mt-2 font-display text-lg text-brand-ink">Lisa Steven</p>
                        <p class="text-sm text-brand-muted">Also known as Lisslamode · Operations and delivery</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-warm-300/40 bg-warm-200/50 py-16 sm:py-24 dark:border-slate-700/40 dark:bg-navy-900/60">
        <div class="mx-auto max-w-7xl px-4 sm:px-8">
            <div class="grid gap-6 lg:grid-cols-3">
                <article class="reveal rounded-3xl border border-warm-300/50 bg-warm-100 p-7 shadow-sm dark:border-slate-700/50 dark:bg-navy-800">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-primary">Mission</span>
                    <h3 class="mt-3 font-display text-2xl text-brand-ink">Convert client ideas into stable, revenue-supporting digital products with fast execution cycles.</h3>
                </article>

                <article class="reveal reveal-delay-1 rounded-3xl border border-warm-300/50 bg-warm-100 p-7 shadow-sm dark:border-slate-700/50 dark:bg-navy-800">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-primary">Vision</span>
                    <h3 class="mt-3 font-display text-2xl text-brand-ink">Become a trusted East African digital execution partner recognized for quality, speed, and accountability.</h3>
                </article>

                <article class="reveal reveal-delay-2 rounded-3xl border border-warm-300/50 bg-warm-100 p-7 shadow-sm dark:border-slate-700/50 dark:bg-navy-800">
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-primary">Goal</span>
                    <h3 class="mt-3 font-display text-2xl text-brand-ink">Build systems that improve operations, create employment opportunities, and scale community impact.</h3>
                </article>
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-8">
            <h2 class="reveal font-display text-3xl font-bold text-brand-ink sm:text-4xl">Ready to build with Town Colors?</h2>
            <p class="reveal reveal-delay-1 mt-4 text-base leading-8 text-brand-muted">
                We partner with ambitious teams to design, build, and scale digital products that deliver measurable business value.
            </p>
            <div class="reveal reveal-delay-2 mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('contact.show') }}" class="btn-primary">Contact Us</a>
                <a href="{{ route('services.index') }}" class="btn-secondary">Explore Services</a>
            </div>
        </div>
    </section>

</x-public-layout>
