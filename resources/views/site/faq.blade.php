<x-public-layout title="FAQ">

    @push('head')
    <meta name="description" content="Frequently asked questions about Town Colors services, custom builds, support, delivery timelines, and hosting.">
    <link rel="canonical" href="{{ route('faq.index') }}">
    @endpush

    <section class="relative overflow-hidden border-b border-warm-300/40 bg-warm-100 py-16 sm:py-24 dark:border-slate-700/40 dark:bg-navy-900">
        <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-brand-primary/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 right-0 h-72 w-72 rounded-full bg-accent/10 blur-3xl"></div>

        <div class="relative mx-auto max-w-5xl px-4 text-center sm:px-8">
            <span class="reveal inline-flex rounded-full border border-accent/30 bg-accent-light px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.25em] text-brand-primary">
                Support Center
            </span>
            <h1 class="reveal reveal-delay-1 mt-5 font-display text-3xl font-bold leading-tight text-brand-ink sm:text-5xl">
                Frequently Asked Questions
            </h1>
            <p class="reveal reveal-delay-2 mx-auto mt-5 max-w-3xl text-base leading-8 text-brand-muted sm:text-lg">
                Find clear answers by category and expand only what you need.
            </p>
        </div>
    </section>

    <section class="py-16 sm:py-24" x-data="{ openCategory: '{{ $faqs->keys()->first() ?? '' }}' }">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-8">
            @forelse ($faqs as $category => $items)
                <section class="reveal rounded-3xl border border-warm-300/50 bg-warm-100/75 p-6 shadow-sm backdrop-blur-sm sm:p-8 dark:border-slate-700/50 dark:bg-navy-800/75" x-data="{ openQuestion: null }">
                    <button type="button" @click="openCategory = openCategory === '{{ $category }}' ? '' : '{{ $category }}'" class="flex w-full items-center justify-between gap-3 text-left">
                        <div>
                            <h2 class="font-display text-2xl text-brand-ink">{{ $category }}</h2>
                            <p class="text-xs text-brand-muted">{{ $items->count() }} Questions</p>
                        </div>
                        <svg class="h-5 w-5 shrink-0 text-brand-muted transition" :class="openCategory === '{{ $category }}' ? 'rotate-180 text-brand-primary' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div class="mt-4 space-y-3" x-show="openCategory === '{{ $category }}'" x-transition>
                        @foreach ($items as $item)
                            <article class="rounded-2xl border border-warm-300/50 bg-warm-100/80 p-4 dark:border-slate-700/50 dark:bg-navy-800/80">
                                <button type="button" @click="openQuestion = openQuestion === {{ $item->id }} ? null : {{ $item->id }}" class="flex w-full items-center justify-between gap-3 text-left">
                                    <h3 class="font-display text-lg text-brand-ink">{{ $item->question }}</h3>
                                    <svg class="h-5 w-5 shrink-0 text-brand-muted transition" :class="openQuestion === {{ $item->id }} ? 'rotate-180 text-brand-primary' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
                                </button>
                                <div x-show="openQuestion === {{ $item->id }}" x-transition class="mt-3 text-sm leading-8 text-brand-muted">
                                    {{ $item->answer }}
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @empty
                <div class="rounded-3xl border border-dashed border-warm-300/50 bg-warm-100/70 p-12 text-center backdrop-blur-sm dark:border-slate-700/50 dark:bg-navy-800/70">
                    <h2 class="font-display text-2xl text-brand-ink">No FAQs published yet</h2>
                    <p class="mt-2 text-sm text-brand-muted">Your admin FAQ entries will appear here once added and marked active.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section class="border-t border-warm-300/40 bg-warm-200/50 py-16 dark:border-slate-700/40 dark:bg-navy-900/60">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-8">
            <h2 class="reveal font-display text-3xl font-bold text-brand-ink sm:text-4xl">Still have questions?</h2>
            <p class="reveal reveal-delay-1 mt-4 text-base leading-8 text-brand-muted">
                Our team is ready to guide you through scope, timelines, and the best approach for your project.
            </p>
            <div class="reveal reveal-delay-2 mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('contact.show') }}" class="btn-primary">Contact Us</a>
                <a href="{{ route('services.index') }}" class="btn-secondary">View Services</a>
            </div>
        </div>
    </section>

</x-public-layout>
