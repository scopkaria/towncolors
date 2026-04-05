<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
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
    </x-slot>

    <div class="space-y-8">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($dashboard['stats'] as $i => $stat)
                <article class="card-premium relative overflow-hidden rounded-3xl border border-white/70 bg-white/90 p-5 shadow-card">
                    {{-- Admin: first card gets accent corner --}}
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
            <article class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Highlights</p>
                        <h2 class="mt-2 font-display text-2xl text-brand-ink">{{ $role->label() }} dashboard overview</h2>
                    </div>
                    <div class="hidden rounded-2xl border border-orange-100 bg-orange-50 px-4 py-3 text-right text-sm text-brand-muted sm:block">
                        <p class="font-semibold text-brand-ink">Focused workspace</p>
                        <p>Designed for fast daily decisions.</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-3">
                    @foreach ($dashboard['highlights'] as $highlight)
                        <div class="card-premium rounded-3xl border border-stone-200/80 bg-stone-50 p-5">
                            <h3 class="font-display text-xl text-brand-ink">{{ $highlight['title'] }}</h3>
                            <p class="mt-3 text-sm leading-7 text-brand-muted">{{ $highlight['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-3xl border border-white/70 bg-slate-950 p-6 text-white shadow-panel">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-300">Live feed</p>
                        <h2 class="mt-2 font-display text-2xl">Recent activity</h2>
                    </div>
                    <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.28em] text-white/70">Today</span>
                </div>

                <div class="mt-6 space-y-3">
                    @forelse ($dashboard['activity'] as $item)
                        <div class="card-premium rounded-3xl border border-white/10 bg-white/5 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <p class="text-sm font-medium leading-6 text-white/90">{{ $item['title'] }}</p>
                                <span class="shrink-0 text-xs uppercase tracking-[0.2em] text-orange-200/80">{{ $item['time'] }}</span>
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
            @php $ai = $dashboard['ai_insights']; @endphp
            <section>
                <article class="rounded-3xl border border-orange-100 bg-gradient-to-br from-orange-50 to-amber-50 p-6 shadow-panel">
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
                        <span class="rounded-full border border-orange-200 bg-white/70 px-3 py-1 text-xs uppercase tracking-[0.28em] text-brand-primary">Rule-based</span>
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
    </div>
</x-app-layout>