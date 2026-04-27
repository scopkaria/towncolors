<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                My Subscription
            </span>
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Your Plan</h1>
                    @if ($isSubscribed)
                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.15em] text-emerald-700">Subscribed</span>
                    @else
                        <span class="inline-flex rounded-full bg-warm-200 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.15em] text-warm-700">Not Subscribed</span>
                    @endif
                </div>
                <p class="max-w-2xl text-sm leading-7 text-brand-muted">Manage your subscription, check your status, and explore upgrade options.</p>
                <p class="text-xs text-brand-muted">Live updates are enabled. This page refreshes automatically when your subscription status changes.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">

        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 7000)"
                 class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H2.645c-1.73 0-2.813-1.874-1.948-3.374L10.052 3.378c.866-1.5 3.032-1.5 3.898 0L21.303 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <div>
                    <p class="font-semibold">Could not submit subscription request.</p>
                    <ul class="mt-1 list-inside list-disc space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Request status timeline --}}
        @if ($requestHistory->isNotEmpty())
            <section>
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.24em] text-brand-muted">Request Activity</h2>
                <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-warm-300/40">
                                <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Plan</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Billing</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Status</th>
                                <th class="hidden px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted md:table-cell">Submitted</th>
                                <th class="hidden px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted lg:table-cell">Reviewed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-warm-200/50">
                            @foreach ($requestHistory as $req)
                                <tr class="hover:bg-warm-200/50">
                                    <td class="px-5 py-4 font-medium text-brand-ink">{{ $req->plan?->name ?? '—' }}</td>
                                    <td class="px-5 py-4 capitalize text-brand-muted">{{ $req->billing_cycle }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $req->statusBadge() }}">
                                            {{ ucfirst($req->status) }}
                                        </span>
                                        @if ($req->admin_notes)
                                            <p class="mt-1 text-xs text-brand-muted">{{ $req->admin_notes }}</p>
                                        @endif
                                    </td>
                                    <td class="hidden px-5 py-4 text-brand-muted md:table-cell">{{ $req->created_at->format('M d, Y') }}</td>
                                    <td class="hidden px-5 py-4 text-brand-muted lg:table-cell">
                                        {{ $req->reviewed_at?->format('M d, Y') ?? 'Pending' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                 class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
                <svg class="h-4 w-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H2.645c-1.73 0-2.813-1.874-1.948-3.374L10.052 3.378c.866-1.5 3.032-1.5 3.898 0L21.303 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if (!$hasFullAccess)
            <section class="rounded-3xl border border-amber-200 bg-amber-50 p-6 shadow-card">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-display text-xl text-amber-900">Limited Access Mode</h2>
                        <p class="mt-1 text-sm text-amber-800">Messages and My Files are locked until you start a free trial or get an active subscription.</p>
                        @if ($user->hasUsedTrial())
                            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.2em] text-red-700">Your free trial has expired. Subscribe to continue.</p>
                        @endif
                    </div>
                    @if (!$user->hasUsedTrial())
                        <form method="POST" action="{{ route('client.trial.start') }}">
                            @csrf
                            <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                Start Free Trial (5 Days)
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        @endif

        {{-- ── Expiry alert ─────────────────────────────────────────────────── --}}
        @if ($subscription && $subscription->isExpiringSoon())
            <div class="flex items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-amber-800">
                        ⚡ Your subscription expires in <strong>{{ $subscription->daysUntilExpiry() }} day(s)</strong>
                    </p>
                    <p class="mt-0.5 text-xs text-amber-700">Renew now to keep uninterrupted access to all features.</p>
                </div>
                <a href="{{ route('contact.show') }}"
                   class="shrink-0 rounded-xl border border-amber-300 bg-warm-100 px-4 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-50">
                    Renew Now
                </a>
            </div>
        @endif

        @if ($subscription && $subscription->status === 'expired')
            <div class="flex items-start gap-4 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H2.645c-1.73 0-2.813-1.874-1.948-3.374L10.052 3.378c.866-1.5 3.032-1.5 3.898 0L21.303 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-800">Your subscription has expired.</p>
                    <p class="mt-0.5 text-xs text-red-700">Contact us to renew and regain full access.</p>
                </div>
                <a href="{{ route('contact.show') }}"
                   class="shrink-0 rounded-xl border border-red-300 bg-warm-100 px-4 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50">
                    Contact Us
                </a>
            </div>
        @endif

        {{-- ── Current plan card ─────────────────────────────────────────────── --}}
        @if ($subscription)
            @php
                $plan    = $subscription->plan;
                $accent  = match($plan?->color ?? 'green') {
                    'blue'   => ['border' => 'border-blue-200',   'bg' => 'bg-blue-50',   'text' => 'text-blue-700',   'dot' => 'bg-blue-500'],
                    'purple' => ['border' => 'border-purple-200', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500'],
                    'black'  => ['border' => 'border-slate-800',  'bg' => 'bg-navy-800', 'text' => 'text-white',       'dot' => 'bg-warm-100'],
                    default  => ['border' => 'border-emerald-200','bg' => 'bg-emerald-50','text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
                };
                $daysLeft = $subscription->daysUntilExpiry();
                $daysColor = $daysLeft <= 5 ? 'text-red-600 font-bold' : ($daysLeft <= 14 ? 'text-amber-600 font-semibold' : '');
            @endphp
            <section>
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.24em] text-brand-muted">Current Plan</h2>
                <article class="rounded-3xl border {{ $accent['border'] }} {{ $accent['bg'] }} p-7 shadow-card">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full {{ $accent['dot'] }}"></span>
                                <span class="text-xs font-bold uppercase tracking-widest {{ $accent['text'] }}">
                                    {{ ucfirst($plan?->color ?? 'green') }} tier
                                </span>
                            </div>
                            <h3 class="mt-3 font-display text-3xl {{ $accent['text'] }}">{{ $plan?->name ?? 'Unknown Plan' }}</h3>
                            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm {{ $accent['text'] }}">
                                <span><span class="opacity-70">Billing:</span> {{ ucfirst($subscription->billing_cycle) }}</span>
                                <span><span class="opacity-70">Active since:</span> {{ $subscription->start_date->format('M d, Y') }}</span>
                                <span><span class="opacity-70">Expires:</span> {{ $subscription->expiry_date->format('M d, Y') }}</span>
                                <span class="{{ $daysColor }}">
                                    @if ($daysLeft > 0)
                                        {{ $daysLeft }} day(s) remaining
                                    @else
                                        Expired
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="flex shrink-0 flex-col items-start gap-3 sm:items-end">
                            <span class="rounded-full border px-3 py-1.5 text-xs font-bold uppercase tracking-wider {{ $subscription->statusBadge() }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('contact.show') }}"
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-current/30 bg-white/20 px-4 py-2 text-xs font-semibold {{ $accent['text'] }} backdrop-blur-sm transition hover:bg-white/30">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Renew
                                </a>
                                <a href="#available-plans"
                                   class="inline-flex items-center gap-1.5 rounded-xl border border-current/30 bg-white/20 px-4 py-2 text-xs font-semibold {{ $accent['text'] }} backdrop-blur-sm transition hover:bg-white/30">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                                    </svg>
                                    Upgrade
                                </a>
                            </div>
                        </div>
                    </div>

                    @if ($plan && $plan->features)
                        <div class="mt-6 grid gap-2 sm:grid-cols-2">
                            @foreach ($plan->features as $feature)
                                <div class="flex items-center gap-2 {{ $accent['text'] }}">
                                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <span class="text-sm opacity-90">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>
            </section>
        @else
            <section class="rounded-3xl border border-dashed border-warm-400/50 bg-warm-200/50 p-12 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-warm-200">
                    <svg class="h-7 w-7 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.745 3.745 0 0 1 3.296-1.043A3.745 3.745 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 0 1 3.296 1.043 3.745 3.745 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                </div>
                @if ($pendingRequest)
                    <p class="mt-4 font-semibold text-brand-ink">Subscription request pending</p>
                    <p class="mt-1 text-sm text-brand-muted">
                        Your request for <strong>{{ $pendingRequest->plan?->name }}</strong> ({{ ucfirst($pendingRequest->billing_cycle) }}) is under review.
                        We'll notify you once it's approved.
                    </p>
                    <span class="mt-3 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                        Awaiting Approval
                    </span>
                @else
                    <p class="mt-4 font-semibold text-brand-ink">No active subscription</p>
                    <p class="mt-1 text-sm text-brand-muted">Choose a plan below to get started.</p>
                @endif
            </section>
        @endif

        {{-- Plan comparison --}}
        @if ($plans->isNotEmpty())
            <section id="available-plans">
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.24em] text-brand-muted">Available Plans</h2>
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($plans as $plan)
                        @php
                            $isCurrent = $subscription && $subscription->plan_id === $plan->id && $subscription->isActive();
                            $accent = match($plan->color) {
                                'blue'   => ['ring' => 'ring-blue-200',   'dot' => 'bg-blue-500',   'badge' => 'bg-blue-50 text-blue-700 border-blue-200',   'btn' => 'bg-blue-600 hover:bg-blue-700'],
                                'purple' => ['ring' => 'ring-purple-200', 'dot' => 'bg-purple-500', 'badge' => 'bg-purple-50 text-purple-700 border-purple-200', 'btn' => 'bg-purple-600 hover:bg-purple-700'],
                                'black'  => ['ring' => 'ring-slate-800',  'dot' => 'bg-slate-900',  'badge' => 'bg-slate-900 text-white border-slate-800',     'btn' => 'bg-slate-900 hover:bg-slate-800'],
                                default  => ['ring' => 'ring-emerald-200','dot' => 'bg-emerald-500','badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200','btn' => 'bg-emerald-600 hover:bg-emerald-700'],
                            };
                        @endphp
                        <article class="relative flex flex-col rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card ring-2 {{ $accent['ring'] }} {{ $isCurrent ? 'opacity-80' : '' }}">
                            @if ($isCurrent)
                                <div class="absolute right-4 top-4 rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest text-emerald-700">
                                    Current
                                </div>
                            @endif
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest {{ $accent['badge'] }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $accent['dot'] }}"></span>
                                {{ ucfirst($plan->color) }}
                            </span>
                            <h3 class="mt-3 font-display text-lg text-brand-ink">{{ $plan->name }}</h3>
                            <div class="mt-3">
                                <p class="text-2xl font-bold text-brand-ink">${{ number_format($plan->price_monthly, 2) }}<span class="text-xs font-normal text-brand-muted">/mo</span></p>
                                <p class="text-xs text-brand-muted">${{ number_format($plan->price_yearly, 2) }}/year</p>
                            </div>
                            @if ($plan->features)
                                <ul class="mt-5 flex-1 space-y-2">
                                    @foreach ($plan->features as $feature)
                                        <li class="flex items-start gap-2 text-xs text-brand-muted">
                                            <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mt-5">
                                @if ($isCurrent)
                                    <div class="w-full rounded-xl border border-warm-300/50 bg-warm-200/50 py-2.5 text-center text-xs font-semibold text-brand-muted">
                                        Active Plan
                                    </div>
                                @elseif ($pendingRequest)
                                    <div class="w-full rounded-xl border border-amber-200 bg-amber-50 py-2.5 text-center text-xs font-semibold text-amber-700">
                                        Request Pending
                                    </div>
                                @else
                                    <button type="button"
                                            x-data
                                            @click="$dispatch('open-subscribe', { id: {{ $plan->id }}, name: '{{ addslashes($plan->name) }}', monthly: '{{ number_format($plan->price_monthly, 2) }}', yearly: '{{ number_format($plan->price_yearly, 2) }}' })"
                                            class="w-full rounded-xl px-4 py-2.5 text-xs font-semibold text-white transition {{ $accent['btn'] }}">
                                        Subscribe
                                    </button>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- History --}}
        @if ($history->isNotEmpty())
            <section>
                <h2 class="mb-4 text-sm font-semibold uppercase tracking-[0.24em] text-brand-muted">Subscription History</h2>
                <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-warm-300/40">
                                <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Plan</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Cycle</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Start Date</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Expiry Date</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-warm-200/50">
                            @foreach ($history as $sub)
                                <tr class="hover:bg-warm-200/50">
                                    <td class="px-5 py-4 font-medium text-brand-ink">{{ $sub->plan?->name ?? '—' }}</td>
                                    <td class="px-5 py-4 capitalize text-brand-muted">{{ $sub->billing_cycle }}</td>
                                    <td class="px-5 py-4 text-brand-muted">{{ $sub->start_date->format('M d, Y') }}</td>
                                    <td class="px-5 py-4 text-brand-ink">{{ $sub->expiry_date->format('M d, Y') }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $sub->statusBadge() }}">
                                            {{ ucfirst($sub->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

    </div>

    {{-- ── Subscribe Modal ──────────────────────────────────────────────── --}}
    <div x-data="{ open: false, plan: { id: '', name: '', monthly: '', yearly: '' }, cycle: 'monthly' }"
         @open-subscribe.window="open = true; plan = $event.detail; cycle = 'monthly'"
         @keydown.escape.window="open = false">
        <div x-cloak x-show="open"
             class="fixed inset-0 z-50 flex items-center justify-center bg-navy-900/70 backdrop-blur-sm px-4 py-8">
            <div @click.outside="open = false"
                 class="w-full max-w-md rounded-3xl border border-white/70 bg-warm-100 shadow-panel">

                <div class="flex items-center justify-between px-6 py-5 border-b border-warm-300/40">
                    <div>
                        <h3 class="font-display text-lg text-brand-ink" x-text="'Subscribe to ' + plan.name"></h3>
                        <p class="text-xs text-brand-muted mt-0.5">Choose your billing cycle to continue</p>
                    </div>
                    <button @click="open = false" class="rounded-xl border border-warm-300/50 p-1.5 text-brand-muted hover:text-brand-ink">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.5"><path d="M6 6l8 8M14 6l-8 8" stroke-linecap="round"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('client.subscription-requests.store') }}" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="plan_id" :value="plan.id">

                    {{-- Billing Cycle --}}
                    <div>
                        <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-brand-muted">Billing Cycle</p>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="billing_cycle" value="monthly" x-model="cycle" class="peer sr-only">
                                <div class="rounded-2xl border-2 border-warm-300/50 p-4 text-center transition peer-checked:border-brand-primary peer-checked:bg-brand-primary/5">
                                    <p class="font-semibold text-brand-ink" x-text="'$' + plan.monthly + '/mo'"></p>
                                    <p class="text-xs text-brand-muted mt-0.5">Monthly</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="billing_cycle" value="yearly" x-model="cycle" class="peer sr-only">
                                <div class="rounded-2xl border-2 border-warm-300/50 p-4 text-center transition peer-checked:border-brand-primary peer-checked:bg-brand-primary/5">
                                    <p class="font-semibold text-brand-ink" x-text="'$' + plan.yearly + '/yr'"></p>
                                    <p class="text-xs text-brand-muted mt-0.5">Yearly <span class="text-emerald-600 font-semibold">Save ~17%</span></p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-brand-muted">Payment Method</p>
                        @if (!empty($enabledPaymentMethods))
                            <div class="grid gap-2">
                                @foreach ($enabledPaymentMethods as $methodKey => $methodLabel)
                                    <label class="flex items-center justify-between rounded-xl border border-warm-300/50 px-3 py-2.5">
                                        <span class="text-sm text-brand-ink">{{ $methodLabel }}</span>
                                        <input type="radio" name="payment_method" value="{{ $methodKey }}" class="h-4 w-4 border-warm-400/50 text-brand-primary focus:ring-brand-primary" {{ $loop->first ? 'checked' : '' }}>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <input type="hidden" name="payment_method" value="manual_review">
                            <p class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700">No payment methods are enabled yet. Your request will still be submitted for manual admin review.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-brand-muted mb-2">Payment Reference (optional)</label>
                        <input type="text" name="payment_reference" maxlength="255" placeholder="Transaction / receipt reference"
                               class="w-full rounded-xl border border-warm-300/50 px-3 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-widest text-brand-muted mb-2">Notes (optional)</label>
                        <textarea name="notes" rows="2" maxlength="500"
                                  placeholder="Any specific requirements or questions..."
                                  class="w-full rounded-xl border border-warm-300/50 px-3 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none resize-none"></textarea>
                    </div>

                    {{-- Summary --}}
                    <div class="rounded-2xl border border-warm-300/40 bg-warm-200/50 px-4 py-3 text-sm text-brand-muted">
                        <p>Your request will be reviewed by our team. Once approved, your subscription will be activated and you'll receive a confirmation.</p>
                        @if (!empty($settings->payment_notes))
                            <p class="mt-2 text-xs text-brand-muted">{{ $settings->payment_notes }}</p>
                        @endif
                        @if (!empty($settings->mpesa_paybill))
                            <p class="mt-2 text-xs font-semibold text-brand-ink">M-Pesa / Paybill: {{ $settings->mpesa_paybill }}</p>
                        @endif
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-brand-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-primary/90">
                        Submit Request
                    </button>
                </form>
            </div>
        </div>
    </div>

    @php
        $liveSnapshot = [
            'active_subscription_id' => $subscription?->id,
            'subscription' => $history->first()
                ? [
                    'id' => $history->first()->id,
                    'status' => $history->first()->status,
                    'plan_id' => $history->first()->plan_id,
                    'expiry_date' => optional($history->first()->expiry_date)->toDateString(),
                    'updated_at' => optional($history->first()->updated_at)->toIso8601String(),
                ]
                : null,
            'request' => $pendingRequest
                ? [
                    'id' => $pendingRequest->id,
                    'status' => $pendingRequest->status,
                    'plan_id' => $pendingRequest->plan_id,
                    'updated_at' => optional($pendingRequest->updated_at)->toIso8601String(),
                ]
                : null,
        ];
    @endphp

    <script>
        (function () {
            var endpoint = @json(route('client.subscription.status'));
            var initialState = JSON.stringify(@json($liveSnapshot));
            var isChecking = false;

            async function checkForChanges() {
                if (document.hidden || isChecking) {
                    return;
                }

                isChecking = true;

                try {
                    var response = await fetch(endpoint, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store'
                    });

                    if (!response.ok) {
                        return;
                    }

                    var payload = await response.json();
                    var currentState = JSON.stringify({
                        active_subscription_id: payload.active_subscription_id,
                        subscription: payload.subscription,
                        request: payload.request,
                    });

                    if (currentState !== initialState) {
                        window.location.reload();
                    }
                } catch (error) {
                    // Keep polling even if one request fails.
                } finally {
                    isChecking = false;
                }
            }

            setInterval(checkForChanges, 8000);
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    checkForChanges();
                }
            });
        })();
    </script>

</x-app-layout>
