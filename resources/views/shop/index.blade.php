<x-public-layout title="Shop">
@php $settings = \App\Models\Setting::first(); @endphp

<x-public-hero
    badge="Software Shop"
    title="Business Apps Ready To Purchase"
    :subtitle="$settings?->heroSubtitle('shop') ?: 'Explore production-ready systems with clear pricing and a one-page checkout request flow. Payment methods and manual request rules follow your admin settings automatically.'"
    :media="$settings?->heroMediaUrl('shop')"
/>

<section class="border-b border-warm-300/40 bg-warm-100 py-5 dark:border-slate-700/40 dark:bg-navy-900">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        <div class="flex flex-wrap gap-2">
            @if (!empty($enabledMethods))
                @foreach ($enabledMethods as $label)
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-emerald-700 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">{{ $label }}</span>
                @endforeach
            @else
                <span class="rounded-full border border-accent/30 bg-accent-light px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-brand-primary">Manual request mode enabled</span>
            @endif
        </div>
    </div>
</section>

<section class="py-12 sm:py-16 lg:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        @if ($products->isEmpty())
            <div class="rounded-3xl border border-dashed border-warm-300/50 bg-warm-100/80 p-14 text-center dark:border-slate-700/50 dark:bg-navy-800/80">
                <h2 class="font-display text-2xl text-brand-ink">No products available yet</h2>
                <p class="mt-2 text-sm text-brand-muted">Products added in Admin Shop will appear here automatically.</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($products as $product)
                    <article class="group overflow-hidden rounded-3xl border border-warm-300/40 bg-warm-100/75 shadow-card backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:shadow-panel dark:border-slate-700/40 dark:bg-navy-800/75">
                        <div class="relative h-56 overflow-hidden bg-gradient-to-br from-warm-200 to-accent-light dark:from-navy-800 dark:to-navy-800">
                            @if ($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            @endif
                            <div class="absolute left-3 top-3 flex items-center gap-2">
                                @if ($product->featured)
                                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-amber-800">Featured</span>
                                @endif
                                <span class="rounded-full bg-emerald-600 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.12em] text-white">Product</span>
                            </div>
                            <span class="absolute right-3 top-3 rounded-full border border-white/20 bg-black/35 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.1em] text-white backdrop-blur-sm">{{ $product->currency ?? 'USD' }} {{ number_format((float) ($product->price ?? 0), 2) }}</span>
                        </div>

                        <div class="p-6">
                            <h2 class="font-display text-xl text-brand-ink">{{ $product->title }}</h2>
                            @if ($product->industry || $product->completion_year)
                                <p class="mt-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-brand-primary">{{ $product->industry ?? 'Software Solution' }} @if($product->completion_year)· {{ $product->completion_year }}@endif</p>
                            @endif
                            @if ($product->description)
                                <p class="mt-3 text-sm leading-7 text-brand-muted line-clamp-4">{{ $product->description }}</p>
                            @endif

                            @if (!empty($product->services))
                                <div class="mt-4 flex flex-wrap gap-1.5">
                                    @foreach (array_slice($product->services, 0, 4) as $service)
                                        <span class="rounded-full border border-warm-300/50 bg-warm-200 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-brand-muted dark:border-slate-700/50 dark:bg-navy-700">{{ $service }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if (!empty($product->technologies))
                                <p class="mt-3 text-xs text-brand-muted line-clamp-2"><span class="font-semibold text-brand-ink">Tech:</span> {{ implode(', ', $product->technologies) }}</p>
                            @endif

                            @if ($product->results)
                                <p class="mt-2 text-xs leading-6 text-brand-muted line-clamp-2"><span class="font-semibold text-brand-ink">Value:</span> {{ $product->results }}</p>
                            @endif

                            <div class="mt-5 flex flex-wrap items-center gap-3">
                                <a href="{{ route('shop.show', $product) }}" class="inline-flex items-center gap-2 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-xs font-semibold text-brand-ink transition hover:border-brand-primary hover:text-brand-primary dark:border-slate-700/50 dark:bg-navy-800">
                                    View Details
                                </a>
                                <a href="{{ route('shop.checkout', $product) }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-emerald-700">
                                    {{ empty($enabledMethods) ? 'Request Software' : 'Buy or Request' }}
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                                </a>
                                @if ($product->project_url)
                                    <a href="{{ $product->project_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-2xl border border-accent/30 bg-accent-light px-4 py-2.5 text-xs font-semibold text-brand-primary transition hover:bg-brand-primary hover:text-white">Visit Website</a>
                                @endif
                                @if ($product->purchase_url)
                                    <a href="{{ $product->purchase_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-2.5 text-xs font-semibold text-brand-ink transition hover:border-brand-primary hover:text-brand-primary dark:border-slate-700/50 dark:bg-navy-800">Instant Link</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
</x-public-layout>
