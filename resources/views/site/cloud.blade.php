<x-public-layout title="Cloud & Hosting Solutions">

    @php $settings = \App\Models\Setting::first(); @endphp

    @push('head')
    <meta name="description" content="Reliable and secure cloud hosting, maintenance, support, performance optimization, and security monitoring by Town Colors.">
    <link rel="canonical" href="{{ route('cloud.index') }}">
    @endpush

    <x-public-hero
        badge="Infrastructure"
        title="Cloud Services Built For Reliability"
        :subtitle="$settings?->heroSubtitle('cloud') ?: 'We operate production-ready infrastructure for business websites, software systems, and APIs with proactive support, backup discipline, and performance-focused architecture.'"
        :media="$settings?->heroMediaUrl('cloud')"
    />

    @php
        $cloudServices = [
            [
                'title' => 'Website Hosting',
                'text' => 'Managed hosting with SSL, CDN-ready caching, and uptime monitoring for corporate and marketing websites.',
            ],
            [
                'title' => 'System Hosting',
                'text' => 'Dedicated and segmented environments for ERP-style systems, healthcare software, school platforms, and internal tools.',
            ],
            [
                'title' => 'Maintenance & Support',
                'text' => 'Scheduled updates, incident response, and operational checklists that reduce downtime and deployment risk.',
            ],
            [
                'title' => 'Performance Optimization',
                'text' => 'Database and query tuning, queue optimization, caching strategy, and asset delivery improvements for faster apps.',
            ],
            [
                'title' => 'Security Monitoring',
                'text' => 'Continuous monitoring, patching cadence, access policy reviews, and hardening practices to protect client data.',
            ],
        ];
    @endphp

    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($cloudServices as $index => $service)
                    <article class="reveal reveal-delay-{{ ($index % 3) + 1 }} rounded-3xl border border-warm-300/50 bg-warm-100/75 p-6 shadow-sm backdrop-blur-sm sm:p-7 dark:border-slate-700/50 dark:bg-navy-800/75">
                        <h2 class="font-display text-2xl text-brand-ink">{{ $service['title'] }}</h2>
                        <p class="mt-4 text-sm leading-8 text-brand-muted sm:text-base">{{ $service['text'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-warm-300/50 bg-warm-100/70 px-5 py-4 text-sm text-brand-muted backdrop-blur-sm dark:border-slate-700/50 dark:bg-navy-800/70">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-brand-primary">Ops Promise</p>
                    <p class="mt-1">Monitoring-first operations and transparent issue communication.</p>
                </div>
                <div class="rounded-2xl border border-warm-300/50 bg-warm-100/70 px-5 py-4 text-sm text-brand-muted backdrop-blur-sm dark:border-slate-700/50 dark:bg-navy-800/70">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-brand-primary">Scale Strategy</p>
                    <p class="mt-1">Capacity planning aligned to expected traffic and business growth.</p>
                </div>
                <div class="rounded-2xl border border-warm-300/50 bg-warm-100/70 px-5 py-4 text-sm text-brand-muted backdrop-blur-sm dark:border-slate-700/50 dark:bg-navy-800/70">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-brand-primary">Recovery Readiness</p>
                    <p class="mt-1">Backup, restore, and rollback playbooks tested before critical releases.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="border-t border-warm-300/40 bg-warm-200/50 py-16 dark:border-slate-700/40 dark:bg-navy-900/60">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-8">
            <h2 class="reveal font-display text-3xl font-bold text-brand-ink sm:text-4xl">Need secure hosting for your next platform?</h2>
            <p class="reveal reveal-delay-1 mt-4 text-base leading-8 text-brand-muted">
                Let Town Colors handle your cloud foundation so you can focus on growth, operations, and customer value.
            </p>
            <div class="reveal reveal-delay-2 mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('contact.show') }}" class="btn-primary">Contact Us</a>
                <a href="{{ route('services.index') }}" class="btn-secondary">Explore Services</a>
            </div>
        </div>
    </section>

</x-public-layout>
