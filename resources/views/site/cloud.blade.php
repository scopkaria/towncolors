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
                'slug'  => 'web-hosting',
                'title' => 'Web Hosting',
                'text'  => 'Managed hosting with SSL certificates, CDN-ready caching, and 24/7 uptime monitoring for corporate and marketing websites.',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />',
                'color' => 'text-sky-500',
                'bg'    => 'bg-sky-50 dark:bg-sky-900/20',
            ],
            [
                'slug'  => 'system-hosting',
                'title' => 'System Hosting',
                'text'  => 'Dedicated and segmented environments for ERP-style systems, healthcare software, school platforms, and internal business tools.',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3m0 3h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Zm-3 6h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Z" />',
                'color' => 'text-violet-500',
                'bg'    => 'bg-violet-50 dark:bg-violet-900/20',
            ],
            [
                'slug'  => 'maintenance',
                'title' => 'Maintenance & Support',
                'text'  => 'Scheduled updates, incident response, and operational checklists that reduce downtime and deployment risk for your systems.',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.049.58.025 1.193-.14 1.743" />',
                'color' => 'text-amber-500',
                'bg'    => 'bg-amber-50 dark:bg-amber-900/20',
            ],
            [
                'slug'  => 'performance',
                'title' => 'Performance Optimization',
                'text'  => 'Database tuning, queue optimization, caching strategy, and asset delivery improvements for faster, smoother applications.',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />',
                'color' => 'text-emerald-500',
                'bg'    => 'bg-emerald-50 dark:bg-emerald-900/20',
            ],
            [
                'slug'  => 'security',
                'title' => 'Security Monitoring',
                'text'  => 'Continuous monitoring, patching cadence, access policy reviews, and hardening practices to protect your client data 24/7.',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />',
                'color' => 'text-rose-500',
                'bg'    => 'bg-rose-50 dark:bg-rose-900/20',
            ],
            [
                'slug'  => 'backup-recovery',
                'title' => 'Backup & Recovery',
                'text'  => 'Automated backups, tested restore playbooks, and disaster recovery plans so your data is always safe and recoverable.',
                'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />',
                'color' => 'text-indigo-500',
                'bg'    => 'bg-indigo-50 dark:bg-indigo-900/20',
            ],
        ];
    @endphp

    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-8">

            <div class="reveal mx-auto mb-12 max-w-2xl text-center">
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-3.5 py-1 text-[10px] font-semibold uppercase tracking-[0.25em] text-brand-primary sm:px-4 sm:py-1.5 sm:text-[11px]">
                    Our Cloud Stack
                </span>
                <h2 class="mt-4 font-display text-2xl font-bold text-brand-ink sm:text-3xl lg:text-4xl">
                    Enterprise-grade infrastructure services
                </h2>
                <p class="mt-3 text-sm leading-6 text-brand-muted sm:text-base sm:leading-7">
                    Every service is designed for uptime, security, and scalability — so you can focus on your business.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($cloudServices as $index => $service)
                    <a href="{{ route('cloud.show', $service['slug']) }}"
                       class="reveal reveal-delay-{{ ($index % 3) + 1 }} group relative overflow-hidden rounded-3xl border border-warm-300/50 bg-white p-7 shadow-card backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-brand-primary/30 hover:shadow-panel sm:p-8 dark:border-slate-700/50 dark:bg-navy-800/75 block no-underline">

                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $service['bg'] }} transition-transform duration-300 group-hover:scale-110">
                            <svg class="h-7 w-7 {{ $service['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                {!! $service['icon'] !!}
                            </svg>
                        </div>

                        <h2 class="mt-5 font-display text-xl text-brand-ink transition group-hover:text-brand-primary sm:text-2xl">{{ $service['title'] }}</h2>
                        <p class="mt-3 text-sm leading-7 text-brand-muted sm:text-base sm:leading-8">{{ $service['text'] }}</p>

                        <div class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-brand-primary opacity-80 transition group-hover:opacity-100">
                            Learn more
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </div>

                        <div class="absolute -bottom-1 left-0 right-0 h-1 origin-left scale-x-0 bg-gradient-to-r from-brand-primary to-accent transition-transform duration-300 group-hover:scale-x-100"></div>
                    </a>
                @endforeach
            </div>

            <div class="mt-12 grid gap-5 md:grid-cols-3">
                <div class="reveal rounded-2xl border border-warm-300/50 bg-gradient-to-br from-warm-100 to-white p-6 shadow-sm dark:border-slate-700/50 dark:from-navy-800 dark:to-navy-800/50">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-primary/10">
                        <svg class="h-5 w-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Ops Promise</p>
                    <p class="mt-2 text-sm leading-7 text-brand-muted">Monitoring-first operations with transparent issue communication and proactive incident alerts.</p>
                </div>
                <div class="reveal reveal-delay-1 rounded-2xl border border-warm-300/50 bg-gradient-to-br from-warm-100 to-white p-6 shadow-sm dark:border-slate-700/50 dark:from-navy-800 dark:to-navy-800/50">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-primary/10">
                        <svg class="h-5 w-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5-6L16.5 21m0 0L12 16.5m4.5 4.5V7.5"/></svg>
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Scale Strategy</p>
                    <p class="mt-2 text-sm leading-7 text-brand-muted">Capacity planning aligned to expected traffic and business growth with zero-downtime scaling.</p>
                </div>
                <div class="reveal reveal-delay-2 rounded-2xl border border-warm-300/50 bg-gradient-to-br from-warm-100 to-white p-6 shadow-sm dark:border-slate-700/50 dark:from-navy-800 dark:to-navy-800/50">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-primary/10">
                        <svg class="h-5 w-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/></svg>
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">Recovery Readiness</p>
                    <p class="mt-2 text-sm leading-7 text-brand-muted">Backup, restore, and rollback playbooks tested before critical releases and production deployments.</p>
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
