<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Admin · Lead Management
            </span>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Leads</h1>
                    <p class="text-sm text-brand-muted">Inquiries submitted through the homepage quick-form.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Summary stats --}}
        <div class="grid gap-4 sm:grid-cols-4">
            @foreach ([
                ['label' => 'All',       'value' => $counts['all'],       'q' => null,        'colour' => 'text-brand-ink'],
                ['label' => 'New',       'value' => $counts['new'],       'q' => 'new',       'colour' => 'text-brand-primary'],
                ['label' => 'Contacted', 'value' => $counts['contacted'], 'q' => 'contacted', 'colour' => 'text-sky-600'],
                ['label' => 'Converted', 'value' => $counts['converted'], 'q' => 'converted', 'colour' => 'text-emerald-600'],
            ] as $stat)
                <a href="{{ route('admin.leads.index', $stat['q'] ? ['status' => $stat['q']] : []) }}"
                   class="card-premium rounded-3xl border {{ $currentStatus === $stat['q'] ? 'border-brand-primary/30 ring-1 ring-brand-primary/20' : 'border-white/70' }} bg-white/90 p-5 shadow-card transition hover:border-brand-primary/30">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">{{ $stat['label'] }}</p>
                    <p class="mt-3 font-display text-3xl {{ $stat['colour'] }}">{{ $stat['value'] }}</p>
                </a>
            @endforeach
        </div>

        {{-- Leads table --}}
        <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel">
            @if ($leads->isEmpty())
                <div class="px-8 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-warm-200/50">
                        <svg class="h-7 w-7 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <p class="mt-4 text-sm font-medium text-brand-ink">No leads yet</p>
                    <p class="mt-1 text-xs text-brand-muted">Leads submitted via the homepage form will appear here.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-warm-300/40">
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted">Name</th>
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted">Email</th>
                                <th class="hidden px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted md:table-cell">Project Type</th>
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted">Status</th>
                                <th class="hidden px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted lg:table-cell">Submitted</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-warm-300/40">
                            @foreach ($leads as $lead)
                                @php $badge = $lead->statusBadge(); @endphp
                                <tr class="transition hover:bg-warm-200/60">
                                    <td class="px-6 py-4 font-medium text-brand-ink">{{ $lead->name }}</td>
                                    <td class="px-6 py-4 text-brand-muted">
                                        <a href="mailto:{{ $lead->email }}" class="hover:text-brand-primary">{{ $lead->email }}</a>
                                    </td>
                                    <td class="hidden px-6 py-4 text-brand-muted md:table-cell">
                                        {{ $lead->project_type ? (\App\Models\Lead::projectTypes()[$lead->project_type] ?? $lead->project_type) : '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex rounded-full border px-2.5 py-0.5 text-[11px] font-semibold {{ $badge['class'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="hidden px-6 py-4 text-xs text-brand-muted lg:table-cell">
                                        {{ $lead->created_at->format('M j, Y') }}
                                        <span class="block text-stone-400">{{ $lead->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.leads.show', $lead) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-warm-300/50 bg-warm-100 px-3 py-1.5 text-xs font-semibold text-brand-ink transition hover:border-accent/30 hover:text-brand-primary">
                                            View
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
