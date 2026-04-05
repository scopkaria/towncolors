<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            <span class="inline-flex w-fit rounded-full border border-orange-200 bg-orange-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.32em] text-brand-primary">
                {{ $content['eyebrow'] }}
            </span>
            <div class="space-y-2">
                <h1 class="font-display text-3xl text-brand-ink sm:text-4xl">
                    {{ $content['title'] }}
                </h1>
                <p class="max-w-3xl text-sm leading-7 text-brand-muted sm:text-base">
                    {{ $content['description'] }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 xl:grid-cols-3">
            @foreach ($content['cards'] as $card)
                <article class="card-premium rounded-3xl border border-white/70 bg-white/90 p-6 shadow-card">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-brand-muted">{{ $card['title'] }}</p>
                    <p class="mt-4 font-display text-4xl text-brand-ink">{{ $card['value'] }}</p>
                    <p class="mt-4 text-sm leading-7 text-brand-muted">{{ $card['meta'] }}</p>
                    <div class="mt-4 h-0.5 w-12 rounded-full bg-brand-primary/40"></div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <article class="rounded-3xl border border-white/70 bg-white/90 p-6 shadow-panel">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-brand-primary">Snapshot</p>
                <h2 class="mt-2 font-display text-2xl text-brand-ink">{{ ucfirst($section) }} built for the {{ strtolower($role->label()) }} workflow</h2>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-brand-muted">
                    This view keeps the most relevant {{ $section }} information visible first, with a balance of premium polish, strong hierarchy, and quick decision support.
                </p>
                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="rounded-3xl border border-stone-200/80 bg-stone-50 p-5">
                        <p class="text-sm font-medium text-brand-muted">Responsive layout</p>
                        <p class="mt-3 font-display text-xl text-brand-ink">Sidebar collapses cleanly on smaller screens.</p>
                    </div>
                    <div class="rounded-3xl border border-stone-200/80 bg-stone-50 p-5">
                        <p class="text-sm font-medium text-brand-muted">Motion system</p>
                        <p class="mt-3 font-display text-xl text-brand-ink">Cards and controls use subtle lift and scale states.</p>
                    </div>
                </div>
            </article>

            <article class="rounded-3xl border border-white/70 bg-slate-950 p-6 text-white shadow-panel">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-300">Why it works</p>
                <div class="mt-5 space-y-3">
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                        <p class="font-medium text-white/90">Clear visual hierarchy</p>
                        <p class="mt-2 text-sm leading-7 text-white/70">Large display typography anchors each page and the cards surface the right numbers fast.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                        <p class="font-medium text-white/90">Consistent interaction model</p>
                        <p class="mt-2 text-sm leading-7 text-white/70">Buttons, panels, and navigation follow one motion and spacing language across the product.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                        <p class="font-medium text-white/90">Role-specific framing</p>
                        <p class="mt-2 text-sm leading-7 text-white/70">Each page keeps the same shell while shifting the content emphasis for admin, client, and freelancer work.</p>
                    </div>
                </div>
            </article>
        </section>
    </div>
</x-app-layout>