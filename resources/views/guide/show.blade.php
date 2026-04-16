<x-app-layout>
    <x-slot name="header">
        <div class="space-y-1">
            <span class="inline-flex w-fit rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">{{ $roleLabel }} · User Guide</span>
            <h1 class="font-display text-3xl text-brand-ink">System User Guide</h1>
            <p class="text-sm text-brand-muted">Quick reference for every section in your backend workspace. You can open this tab any time from the sidebar.</p>
        </div>
    </x-slot>

    <div class="space-y-5">
        @foreach ($sections as $section)
            <section class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card">
                <div class="space-y-1">
                    <h2 class="font-display text-xl text-brand-ink">{{ $section['title'] }}</h2>
                    <p class="text-sm text-brand-muted">{{ $section['description'] }}</p>
                </div>

                <div class="mt-4 overflow-hidden rounded-2xl border border-warm-300/40">
                    <table class="min-w-full divide-y divide-warm-300/70 text-sm">
                        <thead class="bg-warm-200/40">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-brand-ink">Section</th>
                                <th class="px-4 py-3 text-left font-semibold text-brand-ink">What It Does</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-warm-300/60 bg-warm-100">
                            @foreach ($section['items'] as $item)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-brand-ink">{{ $item['name'] }}</td>
                                    <td class="px-4 py-3 text-brand-muted">{{ $item['what'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endforeach
    </div>
</x-app-layout>
