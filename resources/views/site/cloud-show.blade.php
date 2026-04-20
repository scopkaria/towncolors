<x-public-layout :title="$serviceData['title'] . ' · Cloud Services'">

@php
    $settings = \App\Models\Setting::first();

    $allServices = [
        'web-hosting' => [
            'title' => 'Web Hosting',
            'subtitle' => 'Managed, secure hosting for corporate and marketing websites with SSL, CDN, and 24/7 monitoring.',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />',
            'color' => 'text-sky-500',
            'bg' => 'bg-sky-50',
            'description' => 'Our web hosting service provides a fully managed environment for your business website. We handle server configuration, SSL certificates, DNS management, and performance optimization so your site loads fast and stays secure.',
            'benefits' => [
                'Free SSL certificates with auto-renewal',
                'CDN integration for global content delivery',
                '99.9% uptime SLA with automated failover',
                'Daily automated backups with 30-day retention',
                'DDoS protection and WAF (Web Application Firewall)',
                'One-click staging environments',
                'HTTP/2 and Brotli compression enabled',
                'Email hosting with spam filtering',
            ],
            'details' => 'Every hosting plan includes proactive monitoring, automatic security patches, and performance tuning. Our infrastructure runs on enterprise-grade hardware with redundant power and networking. We monitor response times, error rates, and resource usage 24/7 to catch and resolve issues before they impact your visitors.',
        ],
        'system-hosting' => [
            'title' => 'System Hosting',
            'subtitle' => 'Dedicated environments for ERP systems, healthcare software, school platforms, and internal business tools.',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 0 1-3-3m3 3a3 3 0 1 0 0 6h13.5a3 3 0 1 0 0-6m-16.5-3a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3m-19.5 0a4.5 4.5 0 0 1 .9-2.7L5.737 5.1a3.375 3.375 0 0 1 2.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 0 1 .9 2.7m0 0a3 3 0 0 1-3 3m0 3h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Zm-3 6h.008v.008h-.008v-.008Zm0-6h.008v.008h-.008v-.008Z" />',
            'color' => 'text-violet-500',
            'bg' => 'bg-violet-50',
            'description' => 'Complex business systems require isolated, high-performance environments. We deploy and manage dedicated servers configured specifically for your application stack, whether it is a hospital management system, school ERP, or custom CRM.',
            'benefits' => [
                'Isolated environments with dedicated resources',
                'Custom server configurations per application',
                'Database clustering and replication',
                'Automated deployment pipelines (CI/CD)',
                'Role-based access control for team members',
                'Compliance-ready infrastructure (HIPAA, GDPR)',
                'Queue workers and scheduled task management',
                'VPN access for secure remote administration',
            ],
            'details' => 'Each system runs in its own containerized or VM-based environment with dedicated CPU, RAM, and storage. We configure database clustering for high availability, set up automated deployment pipelines, and manage queue workers and cron jobs. Access is controlled through SSH keys and VPN tunnels with full audit logging.',
        ],
        'maintenance' => [
            'title' => 'Maintenance & Support',
            'subtitle' => 'Scheduled updates, incident response, and operational checklists to minimize downtime.',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.049.58.025 1.193-.14 1.743" />',
            'color' => 'text-amber-500',
            'bg' => 'bg-amber-50',
            'description' => 'Technology evolves constantly. Our maintenance service keeps your applications up to date, secure, and performing optimally through proactive care rather than reactive firefighting.',
            'benefits' => [
                'Monthly security patches and framework updates',
                'Performance monitoring and optimization',
                'Bug fixes and minor feature adjustments',
                'Database maintenance and optimization',
                'Log monitoring and anomaly detection',
                'Priority support with defined response times',
                'Monthly health report and recommendations',
                'Emergency incident response (24/7)',
            ],
            'details' => 'We follow a structured maintenance calendar: weekly automated checks, monthly patch cycles, and quarterly security audits. Every update goes through a staging environment first. Our incident response process includes immediate triage, root cause analysis, and post-incident reviews to prevent recurrence.',
        ],
        'performance' => [
            'title' => 'Performance Optimization',
            'subtitle' => 'Database tuning, caching strategy, and asset delivery improvements for lightning-fast applications.',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />',
            'color' => 'text-emerald-500',
            'bg' => 'bg-emerald-50',
            'description' => 'Speed directly impacts user experience and revenue. We analyze your application from database queries to frontend rendering, identifying and eliminating bottlenecks that slow things down.',
            'benefits' => [
                'Database query analysis and indexing optimization',
                'Redis/Memcached caching implementation',
                'Image optimization and lazy loading',
                'CDN configuration for static assets',
                'Code profiling and bottleneck identification',
                'API response time optimization',
                'Front-end bundle optimization and tree-shaking',
                'Load testing and capacity planning',
            ],
            'details' => 'Our performance audit starts with real-user monitoring data and synthetic benchmarks. We trace slow requests through the full stack — from DNS resolution to database queries to browser rendering. Results typically include 40-70% faster page loads, significantly reduced server costs, and better search engine rankings.',
        ],
        'security' => [
            'title' => 'Security Monitoring',
            'subtitle' => 'Continuous monitoring, vulnerability scanning, and hardening practices to protect your data.',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />',
            'color' => 'text-rose-500',
            'bg' => 'bg-rose-50',
            'description' => 'Security is not a feature — it is a discipline. We implement layered security controls and continuous monitoring to detect and respond to threats before they cause harm.',
            'benefits' => [
                'Automated vulnerability scanning',
                'SSL/TLS certificate management',
                'Intrusion detection and prevention (IDS/IPS)',
                'Access control and authentication hardening',
                'Regular penetration testing',
                'Security headers and CSP configuration',
                'Malware scanning and removal',
                'Compliance auditing and reporting',
            ],
            'details' => 'Our security stack includes automated vulnerability scanners running daily, real-time intrusion detection, and quarterly manual penetration tests. We enforce strong authentication policies, implement OWASP best practices, and maintain detailed audit logs. Every security incident triggers our documented response playbook.',
        ],
        'backup-recovery' => [
            'title' => 'Backup & Recovery',
            'subtitle' => 'Automated backups, tested restore procedures, and disaster recovery plans for business continuity.',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />',
            'color' => 'text-indigo-500',
            'bg' => 'bg-indigo-50',
            'description' => 'Data loss can cripple a business. Our backup service ensures every critical system has automated, verified backups with tested restore procedures you can rely on in an emergency.',
            'benefits' => [
                'Automated daily backups with offsite storage',
                'Point-in-time database recovery',
                '30-day backup retention as standard',
                'Encrypted backup transmission and storage',
                'Monthly restore verification tests',
                'Documented disaster recovery plan',
                'Cross-region backup replication',
                'Self-service restore for recent snapshots',
            ],
            'details' => 'We use incremental backup strategies to minimize storage while maintaining full recovery capability. Backups are encrypted in transit and at rest, stored across multiple geographic regions. We test restore procedures monthly and document recovery time objectives (RTO) and recovery point objectives (RPO) for each system.',
        ],
    ];

    $serviceData = $allServices[$slug] ?? null;
    if (!$serviceData) abort(404);

    // Build related services list
    $relatedServices = collect($allServices)->except($slug)->take(3);
@endphp

@push('head')
<meta name="description" content="{{ $serviceData['subtitle'] }}">
<link rel="canonical" href="{{ route('cloud.show', $slug) }}">
@endpush

<x-public-hero
    badge="Cloud Service"
    :title="$serviceData['title']"
    :subtitle="$serviceData['subtitle']"
    :media="$settings?->heroMediaUrl('cloud')"
/>

{{-- Service overview --}}
<section class="py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr]">

            <div>
                <article class="reveal rounded-3xl border border-warm-300/50 bg-warm-100 p-7 shadow-sm sm:p-10 dark:border-slate-700/50 dark:bg-navy-800">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl {{ $serviceData['bg'] }}">
                        <svg class="h-8 w-8 {{ $serviceData['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            {!! $serviceData['icon'] !!}
                        </svg>
                    </div>
                    <h2 class="mt-6 font-display text-3xl text-brand-ink">What is {{ $serviceData['title'] }}?</h2>
                    <p class="mt-4 text-sm leading-8 text-brand-muted sm:text-base">{{ $serviceData['description'] }}</p>

                    <div class="mt-8 rounded-2xl border border-warm-300/50 bg-warm-200/50 p-5 dark:border-slate-700/50 dark:bg-navy-800/50">
                        <h3 class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-primary">How It Works</h3>
                        <p class="mt-2 text-sm leading-7 text-brand-muted">{{ $serviceData['details'] }}</p>
                    </div>
                </article>
            </div>

            <aside>
                <div class="reveal reveal-delay-1 rounded-3xl border border-warm-300/50 bg-warm-100 p-7 shadow-sm sm:p-8 dark:border-slate-700/50 dark:bg-navy-800">
                    <h3 class="font-display text-xl text-brand-ink">Key Benefits</h3>
                    <ul class="mt-5 space-y-3">
                        @foreach ($serviceData['benefits'] as $benefit)
                            <li class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                <span class="text-sm leading-6 text-brand-muted">{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="reveal reveal-delay-2 mt-6 rounded-3xl border border-warm-300/50 bg-warm-100 p-7 shadow-sm sm:p-8 dark:border-slate-700/50 dark:bg-navy-800">
                    <h3 class="font-display text-lg text-brand-ink">Why Choose Town Colors?</h3>
                    <p class="mt-3 text-sm leading-7 text-brand-muted">
                        We don't just provide infrastructure — we operate it. Our team has hands-on experience managing production systems for businesses across Africa and beyond. Every service comes with proactive monitoring, documented procedures, and direct access to engineers who know your stack.
                    </p>
                    <div class="mt-5 flex flex-col gap-3">
                        <a href="{{ route('contact.show') }}" class="btn-primary w-full justify-center">Get Started</a>
                        <a href="{{ route('cloud.index') }}" class="btn-secondary w-full justify-center">All Cloud Services</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- Cross-links --}}
<section class="border-t border-warm-300/40 bg-warm-200/50 py-14 sm:py-20 dark:border-slate-700/40 dark:bg-navy-900/60">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        <h2 class="reveal font-display text-2xl text-brand-ink sm:text-3xl">Explore More</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($relatedServices as $rSlug => $rs)
                <a href="{{ route('cloud.show', $rSlug) }}" class="reveal rounded-2xl border border-warm-300/50 bg-warm-100 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-card dark:border-slate-700/50 dark:bg-navy-800">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $rs['bg'] }}">
                        <svg class="h-5 w-5 {{ $rs['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">{!! $rs['icon'] !!}</svg>
                    </div>
                    <p class="mt-3 font-display text-base text-brand-ink">{{ $rs['title'] }}</p>
                </a>
            @endforeach
            <a href="{{ route('services.index') }}" class="reveal rounded-2xl border border-warm-300/50 bg-warm-100 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-card dark:border-slate-700/50 dark:bg-navy-800">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-primary/10">
                    <svg class="h-5 w-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                </div>
                <p class="mt-3 font-display text-base text-brand-ink">All Services</p>
            </a>
        </div>
    </div>
</section>

</x-public-layout>
