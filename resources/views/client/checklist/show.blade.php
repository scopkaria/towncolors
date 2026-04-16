<x-app-layout>
    <x-slot name="header">
        <div class="space-y-2">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">My Progress</span>
            <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">Checklist</h1>
            <p class="text-sm text-brand-muted">Follow the work in progress. Your admin team updates these items for you in real time.</p>
        </div>
    </x-slot>

    <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
        <div class="space-y-4">
            @forelse ($items as $item)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-warm-300/50 px-4 py-4">
                    <div class="flex items-center gap-3">
                        @if ($item->status === 'completed')
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700">✔</span>
                        @elseif ($item->status === 'in_progress')
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">⏳</span>
                        @else
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-warm-200 text-warm-700">•</span>
                        @endif
                        <div>
                            <p class="font-semibold text-brand-ink">{{ $item->title }}</p>
                            <p class="text-xs text-brand-muted">Updated {{ $item->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $item->statusBadge() }}">{{ str_replace('_', ' ', ucfirst($item->status)) }}</span>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-warm-400/50 bg-warm-200/50 p-10 text-center text-sm text-brand-muted">No checklist items yet. Your team will add progress items here.</div>
            @endforelse
        </div>
    </section>

    @php
        $latest = $items->sortByDesc('updated_at')->first();
        $snapshot = [
            'count' => $items->count(),
            'latest' => $latest ? ['id' => $latest->id, 'status' => $latest->status, 'updated_at' => optional($latest->updated_at)->toIso8601String()] : null,
        ];
    @endphp
    <script>
        (function () {
            var endpoint = @json(route('client.checklist.snapshot'));
            var initialState = JSON.stringify(@json($snapshot));
            var busy = false;
            async function check() {
                if (document.hidden || busy) return;
                busy = true;
                try {
                    var response = await fetch(endpoint, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', cache: 'no-store' });
                    if (!response.ok) return;
                    var payload = await response.json();
                    if (JSON.stringify(payload) !== initialState) window.location.reload();
                } catch (e) {
                } finally {
                    busy = false;
                }
            }
            setInterval(check, 8000);
        })();
    </script>
</x-app-layout>