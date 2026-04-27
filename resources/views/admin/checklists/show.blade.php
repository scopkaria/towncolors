<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-1">
                <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">Admin · Checklist</span>
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">{{ $user->name }} Checklist</h1>
                <p class="text-sm text-brand-muted">Track client deliverables and progress items in one place.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form method="POST" action="{{ route('admin.checklists.apply-professional-template', $user) }}">
                    @csrf
                    <button type="submit" class="btn-primary">Apply Professional Monthly Template</button>
                </form>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Back to Users</a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">{{ session('success') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <h2 class="font-display text-xl text-brand-ink">Add Item</h2>
            <form method="POST" action="{{ route('admin.checklists.store', $user) }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Title</label>
                    <input type="text" name="title" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none" placeholder="Website setup">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-brand-ink">Status</label>
                    <select name="status" class="w-full rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary w-full">Add Checklist Item</button>
            </form>
        </section>

        <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
            <h2 class="font-display text-xl text-brand-ink">Current Items</h2>
            <div class="mt-5 space-y-4">
                @forelse ($items as $item)
                    <div class="rounded-2xl border border-warm-300/50 p-4">
                        <form method="POST" action="{{ route('admin.checklists.update', [$user, $item]) }}" class="grid gap-3 md:grid-cols-[1fr_180px_auto_auto] md:items-center">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="title" value="{{ $item->title }}" class="rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                            <select name="status" class="rounded-xl border border-warm-300/50 px-4 py-2.5 text-sm text-brand-ink focus:border-brand-primary focus:outline-none">
                                <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ $item->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ $item->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            <button type="submit" class="rounded-xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-sm font-semibold text-brand-ink">Save</button>
                        </form>
                        <form method="POST" action="{{ route('admin.checklists.destroy', [$user, $item]) }}" class="mt-3" onsubmit="return confirm('Delete this checklist item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-xl border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">Delete</button>
                        </form>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-warm-400/50 bg-warm-200/50 p-10 text-center text-sm text-brand-muted">No checklist items yet.</div>
                @endforelse
            </div>
        </section>
    </div>

    @php
        $snapshot = [
            'count' => $items->count(),
            'latest' => $items->sortByDesc('updated_at')->first() ? [
                'id' => $items->sortByDesc('updated_at')->first()->id,
                'status' => $items->sortByDesc('updated_at')->first()->status,
                'updated_at' => optional($items->sortByDesc('updated_at')->first()->updated_at)->toIso8601String(),
            ] : null,
        ];
    @endphp
    <script>
        (function () {
            var endpoint = @json(route('admin.checklists.snapshot', $user));
            var initialState = JSON.stringify(@json($snapshot));
            var busy = false;
            async function check() {
                if (document.hidden || busy) return;
                busy = true;
                try {
                    var response = await fetch(endpoint, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', cache: 'no-store' });
                    if (!response.ok) return;
                    var payload = await response.json();
                    var state = JSON.stringify(payload);
                    if (state !== initialState) window.location.reload();
                } catch (e) {
                } finally {
                    busy = false;
                }
            }
            setInterval(check, 8000);
        })();
    </script>
</x-app-layout>