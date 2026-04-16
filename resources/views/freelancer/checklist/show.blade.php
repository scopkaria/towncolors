<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Freelancer · Checklist</span>
            <h1 class="font-display text-3xl text-brand-ink">Assigned Checklists</h1>
            <p class="text-sm text-brand-muted">Read-only view of client checklist progress for your assigned projects.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @forelse ($clients as $client)
            <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl text-brand-ink">{{ $client->name }}</h2>
                        <p class="text-xs text-brand-muted">{{ $client->email }}</p>
                    </div>
                    @php
                        $completed = $client->checklistItems->where('status', 'completed')->count();
                        $total = $client->checklistItems->count();
                    @endphp
                    <span class="rounded-full bg-warm-200 px-3 py-1 text-xs font-semibold text-stone-700">{{ $completed }} / {{ $total }} completed</span>
                </div>

                @if ($client->checklistItems->isEmpty())
                    <p class="rounded-2xl border border-dashed border-warm-400/50 bg-warm-200/50 px-4 py-3 text-sm text-brand-muted">No checklist items available.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($client->checklistItems as $item)
                            <div class="flex items-center justify-between rounded-2xl border border-warm-300/50 bg-warm-200/50 px-4 py-3">
                                <div>
                                    <p class="text-sm font-semibold text-brand-ink">{{ $item->title }}</p>
                                    <p class="text-xs text-brand-muted">Updated {{ $item->updated_at->diffForHumans() }}</p>
                                </div>
                                <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide {{ $item->statusBadge() }}">{{ str_replace('_', ' ', $item->status) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @empty
            <section class="rounded-3xl border border-dashed border-warm-400/50 bg-warm-200/50 p-12 text-center">
                <h2 class="font-display text-2xl text-brand-ink">No Assigned Client Checklists Yet</h2>
                <p class="mt-2 text-sm text-brand-muted">Checklists will appear here when you are assigned to client projects.</p>
            </section>
        @endforelse
    </div>
</x-app-layout>
