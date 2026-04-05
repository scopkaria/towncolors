@php
    $d        = $dashboard;
    $trend    = $d['revenue_trend'];
    $trendUp  = $trend >= 0;
    $statuses = [
        'pending'     => ['label' => 'Pending',     'class' => 'bg-amber-100  text-amber-800  border-amber-200'],
        'assigned'    => ['label' => 'Assigned',    'class' => 'bg-sky-100    text-sky-800    border-sky-200'],
        'in_progress' => ['label' => 'In Progress', 'class' => 'bg-violet-100 text-violet-800 border-violet-200'],
        'completed'   => ['label' => 'Completed',   'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
    ];
@endphp

{{-- ══════════════════════════════════════════════════════════════════════
     ROW 1: Revenue Summary · Active Projects
     ══════════════════════════════════════════════════════════════════════ --}}
<section class="grid gap-6 xl:grid-cols-[1fr_1.6fr]">

    {{-- Revenue Summary card --}}
    <article class="flex flex-col rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Revenue Summary</p>
        <h2 class="mt-2 font-display text-2xl text-brand-ink">Financial overview</h2>

        {{-- KPI strip --}}
        <div class="mt-6 grid grid-cols-2 gap-3">
            <div class="rounded-2xl border border-stone-100 bg-stone-50 p-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-muted">This month</p>
                <p class="mt-2 font-display text-2xl text-brand-ink">
                    TZS {{ number_format($d['revenue_this_month'], 0) }}
                </p>
                <span class="mt-2 inline-flex items-center gap-1 text-xs font-semibold
                    {{ $trendUp ? 'text-emerald-600' : 'text-red-500' }}">
                    @if ($trendUp)
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18"/>
                        </svg>
                    @else
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3"/>
                        </svg>
                    @endif
                    {{ abs($trend) }}% vs last month
                </span>
            </div>
            <div class="rounded-2xl border border-stone-100 bg-stone-50 p-4">
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-muted">Outstanding</p>
                <p class="mt-2 font-display text-2xl text-brand-ink">
                    TZS {{ number_format($d['outstanding_amount'], 0) }}
                </p>
                <p class="mt-2 text-xs text-brand-muted">Awaiting collection</p>
            </div>
        </div>

        {{-- Sparkline chart --}}
        <div class="mt-6 flex-1">
            <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-muted">6-month revenue trend</p>
            <div class="relative mt-3 overflow-hidden rounded-2xl border border-stone-100 bg-gradient-to-br from-stone-50 to-orange-50/30 p-3">
                <svg viewBox="0 0 200 44" class="h-14 w-full overflow-visible" preserveAspectRatio="none" aria-hidden="true">
                    {{-- Gradient fill under line --}}
                    <defs>
                        <linearGradient id="rev-grad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#f97316" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#f97316" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <polygon
                        points="{{ $d['sparkline_points'] }} 200,44 0,44"
                        fill="url(#rev-grad)"
                    />
                    <polyline
                        points="{{ $d['sparkline_points'] }}"
                        fill="none"
                        stroke="#f97316"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        vector-effect="non-scaling-stroke"
                    />
                </svg>
                {{-- Month labels --}}
                <div class="mt-1 flex justify-between">
                    @foreach ($d['sparkline_months'] as $m)
                        <span class="text-[10px] text-brand-muted">{{ $m['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <a href="{{ route('admin.invoices') }}" class="mt-5 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-primary hover:underline">
            View invoices
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </article>

    {{-- Active Projects list --}}
    <article class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Active Projects</p>
                <h2 class="mt-2 font-display text-2xl text-brand-ink">Live pipeline</h2>
            </div>
            <a href="{{ route('admin.projects.index') }}"
               class="shrink-0 rounded-2xl border border-stone-200 bg-stone-50 px-3 py-1.5 text-xs font-semibold text-brand-muted transition hover:text-brand-primary">
                View all →
            </a>
        </div>

        @if (count($d['active_projects_list']) > 0)
            <div class="mt-5 space-y-2">
                @foreach ($d['active_projects_list'] as $proj)
                    @php $st = $statuses[$proj['status']] ?? ['label' => ucfirst($proj['status']), 'class' => 'bg-stone-100 text-stone-700 border-stone-200']; @endphp
                    <a href="{{ route('admin.projects.show', $proj['id']) }}"
                       class="flex items-center gap-3 rounded-2xl border border-stone-100 bg-stone-50/60 px-4 py-3 transition hover:border-orange-200 hover:bg-orange-50/40">
                        {{-- Status dot --}}
                        <span class="h-2 w-2 shrink-0 rounded-full
                            {{ str_contains($proj['status'], 'progress') ? 'bg-violet-500' :
                               ($proj['status'] === 'assigned' ? 'bg-sky-500' : 'bg-amber-400') }}">
                        </span>
                        {{-- Title + meta --}}
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-brand-ink">{{ $proj['title'] }}</p>
                            <p class="mt-0.5 truncate text-xs text-brand-muted">
                                {{ $proj['client'] }}
                                @if ($proj['freelancer'] !== 'Unassigned')
                                    · <span class="text-violet-600">{{ $proj['freelancer'] }}</span>
                                @else
                                    · <span class="italic text-amber-600">Unassigned</span>
                                @endif
                            </p>
                        </div>
                        {{-- Status badge --}}
                        <span class="shrink-0 rounded-full border px-2.5 py-0.5 text-[10px] font-semibold {{ $st['class'] }}">
                            {{ $st['label'] }}
                        </span>
                        {{-- Time --}}
                        <span class="hidden shrink-0 text-[10px] text-brand-muted sm:block">{{ $proj['updated'] }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="mt-6 flex flex-col items-center rounded-2xl border border-dashed border-stone-200 py-10 text-center">
                <svg class="h-8 w-8 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75"/>
                </svg>
                <p class="mt-3 text-sm text-brand-muted">No active projects right now.</p>
            </div>
        @endif
    </article>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     ROW 2: Top Freelancers · Recent Activity feed
     ══════════════════════════════════════════════════════════════════════ --}}
<section class="grid gap-6 xl:grid-cols-[1fr_1fr]">

    {{-- Top Freelancers --}}
    <article class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Top Freelancers</p>
        <h2 class="mt-2 font-display text-2xl text-brand-ink">Delivery leaders</h2>

        @if (count($d['top_freelancers']) > 0)
            <div class="mt-5 space-y-4">
                @foreach ($d['top_freelancers'] as $i => $fl)
                    @php
                        $barPct = $d['max_fl_projects'] > 0
                            ? round(($fl['total'] / $d['max_fl_projects']) * 100)
                            : 0;
                        $avatarColors = ['bg-violet-100 text-violet-700', 'bg-sky-100 text-sky-700', 'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-800', 'bg-rose-100 text-rose-700'];
                        $color = $avatarColors[$i % count($avatarColors)];
                    @endphp
                    <div class="flex items-center gap-3">
                        {{-- Rank --}}
                        <span class="w-5 shrink-0 text-center text-xs font-bold text-brand-muted">#{{ $i + 1 }}</span>
                        {{-- Avatar --}}
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm font-bold {{ $color }}">
                            {{ $fl['initials'] }}
                        </div>
                        {{-- Info --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="truncate text-sm font-semibold text-brand-ink">{{ $fl['name'] }}</p>
                                <span class="shrink-0 text-xs font-semibold text-brand-ink">
                                    {{ $fl['total'] }} project{{ $fl['total'] !== 1 ? 's' : '' }}
                                </span>
                            </div>
                            {{-- Progress bar --}}
                            <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-stone-100">
                                <div class="h-full rounded-full bg-brand-primary transition-all duration-700"
                                     style="width: {{ $barPct }}%"></div>
                            </div>
                            <div class="mt-1 flex items-center justify-between">
                                <p class="text-[10px] text-brand-muted">
                                    {{ $fl['completed'] }} completed
                                </p>
                                <p class="text-[10px] font-medium text-brand-muted">
                                    TZS {{ number_format($fl['earnings'], 0) }} earned
                                </p>
                            </div>
                        </div>
                    </div>
                    @if (!$loop->last)
                        <div class="h-px bg-stone-100"></div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="mt-6 flex flex-col items-center rounded-2xl border border-dashed border-stone-200 py-10 text-center">
                <svg class="h-8 w-8 text-stone-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                </svg>
                <p class="mt-3 text-sm text-brand-muted">No freelancer activity yet.</p>
            </div>
        @endif
    </article>

    {{-- Rich Recent Activity --}}
    <article class="rounded-3xl border border-white/70 bg-slate-950 p-6 text-white shadow-panel">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-orange-300">Live feed</p>
                <h2 class="mt-2 font-display text-2xl">Recent activity</h2>
            </div>
            <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] uppercase tracking-[0.28em] text-white/60">
                Latest 8
            </span>
        </div>

        @php
            $typeConfig = [
                'project' => [
                    'dot'  => 'bg-violet-400',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/>',
                ],
                'invoice' => [
                    'dot'  => 'bg-emerald-400',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75"/>',
                ],
                'lead' => [
                    'dot'  => 'bg-orange-400',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/>',
                ],
            ];
        @endphp

        <div class="mt-5 space-y-2">
            @forelse ($d['activity_feed'] as $item)
                @php $cfg = $typeConfig[$item['type']] ?? $typeConfig['project']; @endphp
                <div class="flex items-start gap-3 rounded-2xl border border-white/8 bg-white/5 px-4 py-3">
                    {{-- Icon --}}
                    <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-xl bg-white/10">
                        <svg class="h-3.5 w-3.5 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            {!! $cfg['icon'] !!}
                        </svg>
                    </div>
                    {{-- Content --}}
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold leading-5 text-white/90">{{ $item['title'] }}</p>
                        <p class="mt-0.5 truncate text-xs text-white/45">{{ $item['sub'] }}</p>
                    </div>
                    {{-- Time --}}
                    <span class="shrink-0 text-[10px] uppercase tracking-[0.2em] text-white/30">
                        {{ $item['time'] }}
                    </span>
                </div>
            @empty
                <div class="rounded-2xl border border-white/10 bg-white/5 py-8 text-center">
                    <p class="text-sm text-white/40">No recent activity yet.</p>
                </div>
            @endforelse
        </div>
    </article>
</section>

{{-- ══════════════════════════════════════════════════════════════════════
     ROW 3: Leads snapshot strip
     ══════════════════════════════════════════════════════════════════════ --}}
<section>
    <article class="flex flex-wrap items-center justify-between gap-6 rounded-3xl border border-orange-100 bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-5 shadow-panel">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Lead Capture Snapshot</p>
            <h2 class="mt-1 font-display text-xl text-brand-ink">
                {{ $d['leads_new'] }} new lead{{ $d['leads_new'] !== 1 ? 's' : '' }} pending
                &nbsp;·&nbsp;
                {{ $d['leads_converted'] }} converted this month
            </h2>
        </div>
        <a href="{{ route('admin.leads.index') }}"
           class="inline-flex items-center gap-2 rounded-2xl border border-orange-200 bg-white px-4 py-2.5 text-sm font-semibold text-brand-primary shadow-sm transition hover:border-brand-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/>
            </svg>
            Manage leads
        </a>
    </article>
</section>
