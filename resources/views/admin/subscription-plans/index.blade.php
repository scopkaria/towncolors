<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Business</span>
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Subscription Plans</h1>
                <p class="text-sm text-brand-muted">Define and manage the plans available to clients.</p>
            </div>
            <a href="{{ route('admin.subscription-plans.create') }}"
               class="inline-flex items-center gap-2 rounded-2xl bg-navy-800 px-4 py-2.5 text-sm font-semibold text-white shadow-card transition hover:bg-slate-800">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Plan
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
            <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($plans->isEmpty())
        <div class="rounded-3xl border border-white/70 bg-white/90 p-16 text-center shadow-panel">
            <p class="text-sm font-semibold text-brand-ink">No plans yet</p>
            <p class="mt-1 text-xs text-brand-muted">Create your first subscription plan to get started.</p>
            <a href="{{ route('admin.subscription-plans.create') }}"
               class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-navy-800 px-5 py-2.5 text-sm font-semibold text-white">
                Create Plan
            </a>
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($plans as $plan)
                @php
                    $accent = match($plan->color) {
                        'blue'   => ['ring' => 'ring-blue-200',   'dot' => 'bg-blue-500',   'badge' => 'bg-blue-50 text-blue-700 border-blue-200'],
                        'purple' => ['ring' => 'ring-purple-200', 'dot' => 'bg-purple-500', 'badge' => 'bg-purple-50 text-purple-700 border-purple-200'],
                        'black'  => ['ring' => 'ring-slate-800',  'dot' => 'bg-slate-900',  'badge' => 'bg-slate-900 text-white border-slate-800'],
                        default  => ['ring' => 'ring-emerald-200','dot' => 'bg-emerald-500','badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    };
                @endphp
                <article class="relative flex flex-col rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card ring-2 {{ $accent['ring'] }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[10px] font-bold uppercase tracking-widest {{ $accent['badge'] }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $accent['dot'] }}"></span>
                                {{ ucfirst($plan->color) }}
                            </span>
                            <h2 class="mt-3 font-display text-xl text-brand-ink">{{ $plan->name }}</h2>
                        </div>
                        @if (!$plan->is_active)
                            <span class="rounded-full bg-warm-200 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-stone-500">Inactive</span>
                        @endif
                    </div>

                    <div class="mt-4 space-y-1">
                        <p class="text-2xl font-bold text-brand-ink">${{ number_format($plan->price_monthly, 2) }}<span class="text-xs font-normal text-brand-muted">/mo</span></p>
                        <p class="text-sm text-brand-muted">${{ number_format($plan->price_yearly, 2) }}/year</p>
                    </div>

                    @if ($plan->features)
                        <ul class="mt-5 space-y-2 text-sm text-brand-muted">
                            @foreach ($plan->features as $feature)
                                <li class="flex items-start gap-2">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-auto pt-6 flex items-center gap-2">
                        <a href="{{ route('admin.subscription-plans.edit', $plan) }}"
                           class="flex-1 rounded-xl border border-warm-300/50 bg-warm-100 py-2 text-center text-xs font-semibold text-brand-ink transition hover:border-brand-primary/40">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.subscription-plans.destroy', $plan) }}"
                              onsubmit="return confirm('Delete this plan? Existing subscriptions will be affected.')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                Delete
                            </button>
                        </form>
                        <span class="rounded-full bg-warm-200 px-2.5 py-1 text-[10px] text-brand-muted">
                            {{ $plan->subscriptions()->count() }} subs
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-app-layout>
