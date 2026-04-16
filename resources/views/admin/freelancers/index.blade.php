<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                Admin · Talent
            </span>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Freelancers 🔥</h1>
                    <p class="text-sm text-brand-muted">All registered freelancer accounts and their project activity.</p>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm font-semibold text-brand-muted shadow-sm">
                        <svg class="h-4 w-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        {{ $freelancers->count() }} {{ Str::plural('freelancer', $freelancers->count()) }}
                    </span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if ($freelancers->isEmpty())
            <div class="rounded-3xl border border-white/70 bg-white/90 px-8 py-16 text-center shadow-panel">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-warm-200/50">
                    <svg class="h-7 w-7 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <p class="mt-4 text-sm font-medium text-brand-ink">No freelancers yet</p>
                <p class="mt-1 text-xs text-brand-muted">Registered freelancer accounts will appear here.</p>
            </div>
        @else
            <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-warm-300/40">
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted">Name</th>
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted">Email</th>
                                <th class="hidden px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted sm:table-cell">Projects</th>
                                <th class="hidden px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted lg:table-cell">Active</th>
                                <th class="hidden px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted lg:table-cell">Invoices</th>
                                <th class="hidden px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-muted xl:table-cell">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-warm-300/40">
                            @foreach ($freelancers as $f)
                                <tr class="transition hover:bg-warm-200/60">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-navy-800 text-xs font-bold text-white">
                                                {{ strtoupper(substr($f->name, 0, 1)) }}
                                            </div>
                                            <span class="font-semibold text-brand-ink">{{ $f->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-brand-muted">
                                        <a href="mailto:{{ $f->email }}" class="hover:text-brand-primary">{{ $f->email }}</a>
                                    </td>
                                    <td class="hidden px-6 py-4 sm:table-cell">
                                        <span class="font-semibold text-brand-ink">{{ $f->assignedProjects }}</span>
                                        <span class="text-brand-muted"> total</span>
                                    </td>
                                    <td class="hidden px-6 py-4 lg:table-cell">
                                        @if ($f->activeProjects > 0)
                                            <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                {{ $f->activeProjects }} active
                                            </span>
                                        @else
                                            <span class="text-xs text-brand-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="hidden px-6 py-4 text-brand-muted lg:table-cell">
                                        {{ $f->invoiceCount ?: '—' }}
                                    </td>
                                    <td class="hidden px-6 py-4 text-xs text-brand-muted xl:table-cell">
                                        {{ $f->created_at->format('M j, Y') }}
                                        <span class="block text-stone-400">{{ $f->created_at->diffForHumans() }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
