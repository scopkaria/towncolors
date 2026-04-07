<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary dark:border-orange-500/30 dark:bg-orange-500/10">
                Admin · Live Chat
            </span>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <h1 class="font-display text-3xl text-brand-ink dark:text-white sm:text-4xl">Live Chat</h1>
                    <p class="text-sm text-brand-muted dark:text-[#A1A1AA]">Incoming visitor conversations from the website.</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Summary stats --}}
        <div class="grid gap-5 sm:grid-cols-3">
            @php
                $waiting = $sessions->where('status', 'waiting')->count();
                $active  = $sessions->where('status', 'active')->count();
                $closed  = $sessions->where('status', 'closed')->count();
            @endphp
            @foreach ([
                ['label' => 'Waiting', 'value' => $waiting, 'colour' => 'text-amber-500',   'bg' => 'bg-amber-50 dark:bg-amber-500/10'],
                ['label' => 'Active',  'value' => $active,  'colour' => 'text-emerald-500', 'bg' => 'bg-emerald-50 dark:bg-emerald-500/10'],
                ['label' => 'Closed',  'value' => $closed,  'colour' => 'text-stone-400',   'bg' => 'bg-stone-50 dark:bg-white/[0.04]'],
            ] as $stat)
                <div class="card-premium rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card dark:border-white/[0.08] dark:bg-[#141416]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted dark:text-[#A1A1AA]">{{ $stat['label'] }}</p>
                    <p class="mt-3 font-display text-3xl {{ $stat['colour'] }}">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Sessions table --}}
        <div class="rounded-3xl border border-white/70 bg-white/90 shadow-panel dark:border-white/[0.08] dark:bg-[#141416]">
            @if ($sessions->isEmpty())
                <div class="px-8 py-16 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-stone-50 dark:bg-white/[0.04]">
                        <svg class="h-7 w-7 text-brand-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                    </div>
                    <p class="mt-4 text-sm text-brand-muted dark:text-[#A1A1AA]">No live chat sessions yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-stone-100 dark:border-white/[0.06]">
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted dark:text-[#A1A1AA]">Visitor</th>
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted dark:text-[#A1A1AA]">Email</th>
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted dark:text-[#A1A1AA]">Status</th>
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted dark:text-[#A1A1AA]">Agent</th>
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted dark:text-[#A1A1AA]">Messages</th>
                                <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted dark:text-[#A1A1AA]">Started</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-50 dark:divide-white/[0.04]">
                            @foreach ($sessions as $session)
                                <tr class="transition hover:bg-stone-50/60 dark:hover:bg-white/[0.02]">
                                    <td class="px-6 py-4 font-medium text-brand-ink dark:text-white">{{ $session->visitor_name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-brand-muted dark:text-[#A1A1AA]">{{ $session->visitor_email ?? '—' }}</td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'waiting' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
                                                'active'  => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
                                                'closed'  => 'bg-stone-100 text-stone-500 dark:bg-white/[0.06] dark:text-[#A1A1AA]',
                                            ];
                                        @endphp
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusColors[$session->status] ?? '' }}">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-brand-muted dark:text-[#A1A1AA]">{{ $session->agent?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-brand-muted dark:text-[#A1A1AA]">{{ $session->messages_count }}</td>
                                    <td class="px-6 py-4 text-brand-muted dark:text-[#A1A1AA]">{{ $session->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.liveChat.show', $session) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl border border-stone-200 bg-white px-3 py-1.5 text-xs font-semibold text-brand-ink transition hover:border-orange-300 hover:text-orange-600
                                                  dark:border-white/[0.10] dark:bg-white/[0.04] dark:text-white dark:hover:border-orange-500 dark:hover:text-orange-400">
                                            {{ $session->status === 'closed' ? 'View' : 'Open' }}
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($sessions->hasPages())
                    <div class="border-t border-stone-100 px-6 py-4 dark:border-white/[0.06]">
                        {{ $sessions->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
