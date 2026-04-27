<x-app-layout>
    <x-slot name="header">
        @if ($role->value === 'admin')
            <div class="space-y-1">
                <p class="text-[11px] font-semibold uppercase tracking-[0.26em] text-brand-muted">Dashboard / Project</p>
                <h1 class="font-display text-2xl text-brand-ink sm:text-3xl">Dashboard</h1>
            </div>
        @else
            <div class="space-y-3">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                    {{ $dashboard['eyebrow'] }}
                </span>
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">
                        {{ $dashboard['title'] }}
                    </h1>
                    <p class="max-w-3xl text-sm leading-7 text-brand-muted sm:text-base">
                        {{ $dashboard['description'] }}
                    </p>
                </div>
            </div>
        @endif
    </x-slot>

    <div class="{{ $role->value === 'admin' ? 'dashboard-admin-compact space-y-4' : 'space-y-8' }}">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($role->value === 'admin')
            @php
                $cardStats = collect($dashboard['stats'])->take(3);
                $sparkMonths = collect($dashboard['sparkline_months'] ?? []);
                $sparkMax = max(1, (float) $sparkMonths->max('amount'));
                $activeProjects = collect($dashboard['active_projects_list'] ?? []);
                $statusRows = collect([
                    ['label' => 'In Progress', 'count' => $activeProjects->where('status', 'in_progress')->count(), 'bar' => 'bg-sky-500'],
                    ['label' => 'Assigned', 'count' => $activeProjects->where('status', 'assigned')->count(), 'bar' => 'bg-amber-500'],
                    ['label' => 'Pending', 'count' => $activeProjects->where('status', 'pending')->count(), 'bar' => 'bg-indigo-500'],
                    ['label' => 'Leads', 'count' => (int) ($dashboard['leads_new'] ?? 0), 'bar' => 'bg-rose-500'],
                ]);
                $statusMax = max(1, (int) $statusRows->max('count'));
            @endphp

            <section class="grid gap-3 lg:grid-cols-3">
                @foreach ($cardStats as $stat)
                    <article class="dashlite-card rounded-2xl border border-white/70 bg-white/90 p-4 shadow-card dark:border-white/[0.08] dark:bg-[#1B2632]">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-brand-muted">{{ $stat['label'] }}</p>
                                <p class="mt-2 font-display text-[1.7rem] leading-none text-brand-ink">{{ $stat['value'] }}</p>
                                <p class="mt-1.5 text-[11px] text-brand-muted">{{ $stat['delta'] }}</p>
                            </div>
                            <div class="mt-1 flex items-end gap-1 opacity-70" aria-hidden="true">
                                <span class="h-4 w-2 rounded-full bg-brand-primary/15"></span>
                                <span class="h-6 w-2 rounded-full bg-brand-primary/25"></span>
                                <span class="h-8 w-2 rounded-full bg-brand-primary/40"></span>
                                <span class="h-5 w-2 rounded-full bg-brand-primary/25"></span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="grid gap-3 xl:grid-cols-[1.7fr_1fr]">
                <article class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-panel dark:border-white/[0.08] dark:bg-[#1B2632]">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-display text-lg text-brand-ink">Tasks Overview</h2>
                        <a href="{{ route('admin.projects.index') }}" class="rounded-lg border border-warm-300/70 bg-warm-100 px-2.5 py-1 text-[11px] font-semibold text-brand-muted transition hover:border-accent/40">Filter</a>
                    </div>

                    <div class="mt-4 overflow-hidden rounded-xl border border-warm-300/50 bg-gradient-to-br from-white to-warm-100/70 px-3 py-3">
                        <div class="flex h-[190px] items-end justify-between gap-3">
                            @forelse ($sparkMonths as $month)
                                @php
                                    $filledDots = max(2, (int) round(((float) $month['amount'] / $sparkMax) * 9));
                                @endphp
                                <div class="flex flex-1 flex-col items-center justify-end gap-1">
                                    @for ($dot = 9; $dot >= 1; $dot--)
                                        <span class="h-2 w-2 rounded-full {{ $dot <= $filledDots ? 'bg-brand-primary/65' : 'bg-brand-primary/15' }}"></span>
                                    @endfor
                                    <span class="pt-1 text-[10px] text-brand-muted">{{ $month['label'] }}</span>
                                </div>
                            @empty
                                <div class="flex h-[190px] w-full items-center justify-center text-sm text-brand-muted">No chart data yet.</div>
                            @endforelse
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-panel dark:border-white/[0.08] dark:bg-[#1B2632]">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-display text-lg text-brand-ink">Project Status</h2>
                        <span class="rounded-lg border border-warm-300/70 bg-warm-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-brand-muted">Monthly</span>
                    </div>

                    <div class="mt-3 space-y-3">
                        @foreach ($statusRows as $row)
                            @php
                                $percent = (int) round(($row['count'] / $statusMax) * 100);
                            @endphp
                            <div>
                                <div class="mb-1 flex items-center justify-between text-xs">
                                    <span class="font-medium text-brand-ink">{{ $row['label'] }}</span>
                                    <span class="text-brand-muted">{{ $row['count'] }}</span>
                                </div>
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-warm-300/65">
                                    <div class="h-full rounded-full {{ $row['bar'] }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 rounded-xl border border-warm-300/50 bg-warm-100/80 px-3 py-2.5">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-muted">Revenue this month</p>
                        <p class="mt-1 font-display text-xl text-brand-ink">TZS {{ number_format($dashboard['revenue_this_month'], 0) }}</p>
                    </div>
                </article>
            </section>

            <section class="grid gap-3 xl:grid-cols-[1.25fr_1.45fr]">
                <article class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-panel dark:border-white/[0.08] dark:bg-[#1B2632]">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-display text-lg text-brand-ink">Timesheet Logged Hours</h2>
                        <span class="rounded-lg border border-warm-300/70 bg-warm-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-brand-muted">Last 7 days</span>
                    </div>

                    <div class="mt-4 rounded-xl border border-warm-300/50 bg-gradient-to-br from-white to-warm-100/70 p-3">
                        <div class="flex h-20 items-end justify-between gap-2">
                            @foreach ($sparkMonths->take(7) as $point)
                                @php
                                    $height = max(12, (int) round(((float) $point['amount'] / $sparkMax) * 72));
                                @endphp
                                <div class="flex flex-1 items-end justify-center">
                                    <span class="w-2.5 rounded-full bg-brand-primary/40" style="height: {{ $height }}px"></span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                        <a href="{{ route('admin.freelancers.index') }}" class="inline-flex items-center justify-center rounded-xl border border-warm-300/80 bg-warm-100 px-3 py-2 font-semibold text-brand-primary transition hover:border-accent/40">Freelancers</a>
                        <a href="{{ route('admin.invoices') }}" class="inline-flex items-center justify-center rounded-xl border border-warm-300/80 bg-warm-100 px-3 py-2 font-semibold text-brand-primary transition hover:border-accent/40">Invoices</a>
                    </div>
                </article>

                <article class="rounded-2xl border border-white/70 bg-white/90 p-4 shadow-panel dark:border-white/[0.08] dark:bg-[#1B2632]">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-display text-lg text-brand-ink">Top Due Projects</h2>
                        <a href="{{ route('admin.projects.index') }}" class="text-[11px] font-semibold text-brand-primary hover:underline">See all</a>
                    </div>

                    <div class="mt-3 overflow-hidden rounded-xl border border-warm-300/50">
                        <table class="min-w-full divide-y divide-warm-300/50 text-xs">
                            <thead class="bg-warm-100/80">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-[0.14em] text-brand-muted">Name</th>
                                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-[0.14em] text-brand-muted">Client</th>
                                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-[0.14em] text-brand-muted">Updated</th>
                                    <th class="px-3 py-2 text-left font-semibold uppercase tracking-[0.14em] text-brand-muted">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-warm-300/40 bg-white/70">
                                @forelse ($activeProjects->take(5) as $proj)
                                    <tr>
                                        <td class="px-3 py-2.5 font-medium text-brand-ink">{{ $proj['title'] }}</td>
                                        <td class="px-3 py-2.5 text-brand-muted">{{ $proj['client'] }}</td>
                                        <td class="px-3 py-2.5 text-brand-muted">{{ $proj['updated'] }}</td>
                                        <td class="px-3 py-2.5">
                                            <span class="inline-flex rounded-full border px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.08em] {{ $proj['status'] === 'in_progress' ? 'border-sky-200 bg-sky-50 text-sky-700' : ($proj['status'] === 'assigned' ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-indigo-200 bg-indigo-50 text-indigo-700') }}">
                                                {{ str_replace('_', ' ', $proj['status']) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-3 py-8 text-center text-sm text-brand-muted">No active projects yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>
        @else

        {{-- Client subscription banner --}}
        @if ($role->value === 'client' && isset($dashboard['subscription']))
            @php $sub = $dashboard['subscription']; @endphp
            @if ($sub['expiring_soon'])
                <div class="flex items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-800">
                            Your <strong>{{ $sub['plan'] }}</strong> plan expires in <strong>{{ $sub['days'] }}</strong> day(s).
                        </p>
                        <p class="mt-0.5 text-xs text-amber-700">Renew soon to keep access to all features.</p>
                    </div>
                    <a href="{{ route('client.subscription.show') }}" class="shrink-0 rounded-xl border border-amber-300 bg-warm-100 px-3 py-1.5 text-xs font-semibold text-amber-800 transition hover:bg-amber-50">
                        View Plan
                    </a>
                </div>
            @elseif ($sub['active'])
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <p class="text-sm font-semibold text-emerald-800">
                            {{ $sub['plan'] }} · {{ $sub['status'] }} — expires {{ $sub['expiry'] }}
                        </p>
                    </div>
                    <a href="{{ route('client.subscription.show') }}" class="shrink-0 text-xs font-semibold text-emerald-700 hover:underline">
                        Manage
                    </a>
                </div>
            @else
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-warm-300/50 bg-warm-200/50 px-5 py-3.5">
                    <p class="text-sm text-brand-muted">No active subscription. Contact us to get started.</p>
                    <a href="{{ route('client.subscription.show') }}" class="shrink-0 text-xs font-semibold text-brand-ink hover:underline">
                        View Plans
                    </a>
                </div>
            @endif
        @endif

        <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($dashboard['stats'] as $i => $stat)
                <article class="card-premium relative overflow-hidden rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card dark:border-white/[0.08] dark:bg-[#1B2632]">
                    @if ($role->value === 'admin' && $i === 0)
                        <div class="absolute right-0 top-0 h-16 w-16 rounded-bl-3xl bg-brand-primary/5"></div>
                    @endif
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">{{ $stat['label'] }}</p>
                    <p class="mt-4 font-display text-3xl text-brand-ink">{{ $stat['value'] }}</p>
                    <p class="mt-3 text-xs leading-5 text-brand-muted">{{ $stat['delta'] }}</p>
                    <div class="mt-4 h-0.5 w-12 rounded-full bg-brand-primary/40"></div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.95fr]">
            <article class="rounded-3xl border border-white/70 bg-white/90 p-7 shadow-panel dark:border-white/[0.08] dark:bg-[#1B2632]">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Highlights</p>
                        <h2 class="mt-2 font-display text-2xl text-brand-ink">{{ $role->label() }} dashboard overview</h2>
                    </div>
                    <div class="hidden rounded-2xl border border-accent/20 bg-accent-light px-4 py-3 text-right text-sm text-brand-muted sm:block dark:border-accent/20 dark:bg-accent/10">
                        <p class="font-semibold text-brand-ink">Focused workspace</p>
                        <p>Designed for fast daily decisions.</p>
                    </div>
                </div>

                <div class="mt-7 grid gap-5 lg:grid-cols-3">
                    @foreach ($dashboard['highlights'] as $highlight)
                        <div class="card-premium rounded-3xl border border-warm-300/80 bg-warm-200/50 p-6 dark:border-white/[0.06] dark:bg-white/[0.03]">
                            <h3 class="font-display text-xl text-brand-ink">{{ $highlight['title'] }}</h3>
                            <p class="mt-3 text-sm leading-7 text-brand-muted">{{ $highlight['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-3xl border border-white/70 bg-navy-800 p-6 text-white shadow-panel">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-accent">Live feed</p>
                        <h2 class="mt-2 font-display text-2xl">Recent activity</h2>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.28em] text-white/70">Today</span>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse ($dashboard['activity'] as $item)
                        <div class="card-premium rounded-3xl border border-white/10 bg-white/5 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <p class="text-sm font-medium leading-6 text-white/90">{{ $item['title'] }}</p>
                                <span class="shrink-0 text-xs uppercase tracking-[0.2em] text-accent/80">{{ $item['time'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-4 text-center">
                            <p class="text-sm text-white/50">No recent activity yet</p>
                        </div>
                    @endforelse
                </div>
            </article>
        </section>

        {{-- AI Insights card — admin only --}}
        @if ($role->value === 'admin' && isset($dashboard['ai_insights']))

        {{-- Client quick links — client only --}}
        @elseif ($role->value === 'client')
            <section class="grid gap-4 sm:grid-cols-2">
                @if (isset($dashboard['subscription']) && $dashboard['subscription']['active'])
                    <a href="{{ route('client.files.index') }}"
                       class="flex items-center gap-5 rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card transition hover:shadow-panel group">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-navy-800 text-white group-hover:bg-slate-800 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-display text-lg text-brand-ink">My Files</p>
                            <p class="mt-0.5 text-sm text-brand-muted">Secure project files are available with your active plan.</p>
                        </div>
                        <svg class="ml-auto h-5 w-5 text-brand-muted group-hover:text-brand-ink transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('client.checklist.show') }}"
                       class="flex items-center gap-5 rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card transition hover:shadow-panel group">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-primary/10 text-brand-primary group-hover:bg-brand-primary/20 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 12.75 10.5 18l8.25-12.75" /></svg>
                        </div>
                        <div>
                            <p class="font-display text-lg text-brand-ink">My Progress</p>
                            <p class="mt-0.5 text-sm text-brand-muted">{{ $dashboard['checklist']['completed'] ?? 0 }} of {{ $dashboard['checklist']['count'] ?? 0 }} checklist items completed</p>
                        </div>
                        <svg class="ml-auto h-5 w-5 text-brand-muted group-hover:text-brand-ink transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @endif
                <a href="{{ route('client.subscription.show') }}"
                   class="flex items-center gap-5 rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card transition hover:shadow-panel group">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-primary/10 text-brand-primary group-hover:bg-brand-primary/20 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.745 3.745 0 0 1 3.296-1.043A3.745 3.745 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 0 1 3.296 1.043 3.745 3.745 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-display text-lg text-brand-ink">My Plan</p>
                        @if (isset($dashboard['subscription']) && $dashboard['subscription']['active'])
                            <p class="mt-0.5 text-sm text-brand-muted">{{ $dashboard['subscription']['plan'] }} · {{ $dashboard['subscription']['status'] }}</p>
                        @else
                            <p class="mt-0.5 text-sm text-brand-muted">No active plan — contact us to subscribe</p>
                        @endif
                    </div>
                    <svg class="ml-auto h-5 w-5 text-brand-muted group-hover:text-brand-ink transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </section>
        @endif

        {{-- AI Insights card — admin only --}}
        @if ($role->value === 'admin' && isset($dashboard['ai_insights']))
            @php $ai = $dashboard['ai_insights']; @endphp
            <section>
                <article class="rounded-3xl border border-accent/20 bg-gradient-to-br from-accent-light to-amber-50 p-6 shadow-panel">
                    {{-- Header --}}
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-brand-primary/10">
                                <svg class="h-5 w-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">AI Insights</p>
                                <h2 class="font-display text-xl text-brand-ink">Today's Platform Summary</h2>
                            </div>
                        </div>
                        <span class="rounded-full border border-accent/30 bg-white/70 px-3 py-1 text-xs uppercase tracking-[0.28em] text-brand-primary">Rule-based</span>
                    </div>

                    {{-- Stats row --}}
                    <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                        <div class="rounded-2xl border border-white/80 bg-white/80 p-4 text-center">
                            <p class="font-display text-2xl text-brand-ink">{{ $ai['newProjectsToday'] }}</p>
                            <p class="mt-1 text-[11px] font-medium uppercase tracking-wide text-brand-muted">New projects</p>
                        </div>
                        <div class="rounded-2xl border border-white/80 bg-white/80 p-4 text-center">
                            <p class="font-display text-2xl text-brand-ink">{{ $ai['pendingInvoices'] }}</p>
                            <p class="mt-1 text-[11px] font-medium uppercase tracking-wide text-brand-muted">Pending invoices</p>
                        </div>
                        <div class="rounded-2xl border border-white/80 bg-white/80 p-4 text-center">
                            <p class="font-display text-2xl text-brand-ink">{{ $ai['unreadMessages'] }}</p>
                            <p class="mt-1 text-[11px] font-medium uppercase tracking-wide text-brand-muted">Messages today</p>
                        </div>
                        <div class="rounded-2xl border border-white/80 bg-white/80 p-4 text-center">
                            <p class="font-display text-2xl text-brand-ink">{{ $ai['activeFreelancers'] }}</p>
                            <p class="mt-1 text-[11px] font-medium uppercase tracking-wide text-brand-muted">Active freelancers</p>
                        </div>
                        <div class="rounded-2xl border {{ $ai['urgentMessagesToday'] > 0 ? 'border-red-200 bg-red-50' : 'border-white/80 bg-white/80' }} p-4 text-center">
                            <p class="font-display text-2xl {{ $ai['urgentMessagesToday'] > 0 ? 'text-red-600' : 'text-brand-ink' }}">{{ $ai['urgentMessagesToday'] }}</p>
                            <p class="mt-1 text-[11px] font-medium uppercase tracking-wide {{ $ai['urgentMessagesToday'] > 0 ? 'text-red-500' : 'text-brand-muted' }}">Urgent messages</p>
                        </div>
                    </div>

                    {{-- Insight bullets --}}
                    @if (!empty($ai['insights']))
                        <div class="mt-6 space-y-2">
                            @foreach ($ai['insights'] as $insight)
                                @php
                                    $colours = match ($insight['level']) {
                                        'alert' => 'border-red-200   bg-red-50   text-red-700',
                                        'warn'  => 'border-amber-200 bg-amber-50 text-amber-800',
                                        default => 'border-sky-200   bg-sky-50   text-sky-800',
                                    };
                                    $dot = match ($insight['level']) {
                                        'alert' => 'bg-red-500',
                                        'warn'  => 'bg-amber-500',
                                        default => 'bg-sky-500',
                                    };
                                @endphp
                                <div class="flex items-start gap-3 rounded-2xl border {{ $colours }} px-4 py-3">
                                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $dot }}"></span>
                                    <p class="text-sm leading-6">{{ $insight['text'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>
            </section>
        @endif
        {{-- Admin smart insights: Revenue · Active Projects · Top Freelancers · Activity --}}
        @if ($role->value === 'admin' && isset($dashboard['activity_feed']))
            @include('dashboard.partials.admin-insights')
        @endif
        @endif
    </div>
</x-app-layout>