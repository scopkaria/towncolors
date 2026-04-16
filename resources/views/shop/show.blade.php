<x-public-layout :title="$product->title . ' · Shop'">

@php
    $gallery = collect($product->product_gallery ?? [])->filter()->values();
    if ($product->image_path) {
        $gallery = $gallery->prepend($product->image_path)->unique()->values();
    }
@endphp

<x-public-hero
    badge="Product Detail"
    :title="$product->title"
    :subtitle="$product->product_description ?: $product->description"
    :media="$product->image_path ? asset('storage/' . $product->image_path) : null"
    heightClass="min-h-[560px] sm:min-h-[680px]"
/>

<section class="py-14 sm:py-20">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-8 lg:grid-cols-[1.1fr_0.9fr]">
        <div>
            @if ($gallery->isNotEmpty())
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($gallery as $imagePath)
                        <button type="button" class="group overflow-hidden rounded-2xl border border-warm-300/50 bg-warm-100 shadow-sm dark:border-slate-700/50 dark:bg-navy-800" x-data="{}" @click="$dispatch('open-gallery-image', { src: '{{ asset('storage/' . $imagePath) }}' })">
                            <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $product->title }}" class="h-56 w-full object-cover transition duration-300 group-hover:scale-105">
                        </button>
                    @endforeach
                </div>
            @endif

            <article class="mt-8 rounded-3xl border border-warm-300/50 bg-warm-100 p-6 shadow-sm sm:p-8 dark:border-slate-700/50 dark:bg-navy-800">
                <h2 class="font-display text-2xl text-brand-ink">Product Overview</h2>
                <p class="mt-4 whitespace-pre-line text-sm leading-8 text-brand-muted sm:text-base">{{ $product->product_description ?: $product->description }}</p>

                @if ($product->extra_info)
                    <div class="mt-7 rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4 dark:border-slate-700/50 dark:bg-navy-800/50">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-primary">Extra Information</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-brand-muted">{{ $product->extra_info }}</p>
                    </div>
                @endif
            </article>
        </div>

        <aside class="rounded-3xl border border-warm-300/50 bg-warm-100 p-6 shadow-sm sm:p-8 dark:border-slate-700/50 dark:bg-navy-800">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-brand-primary">Price</p>
            <p class="mt-2 font-display text-4xl text-brand-ink">{{ $product->currency ?? 'USD' }} {{ number_format((float) ($product->price ?? 0), 2) }}</p>

            @if (!empty($product->services))
                <div class="mt-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Included Modules</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($product->services as $service)
                            <span class="rounded-full border border-warm-300/50 bg-warm-200/50 px-3 py-1 text-[11px] font-semibold text-brand-muted dark:border-slate-700/50 dark:bg-navy-700/50">{{ $service }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($product->technologies))
                <div class="mt-6">
                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-brand-muted">Technology</p>
                    <p class="mt-2 text-sm leading-7 text-brand-muted">{{ implode(', ', $product->technologies) }}</p>
                </div>
            @endif

            <div class="mt-8 flex flex-col gap-3">
                <a href="{{ route('shop.checkout', $product) }}" class="btn-primary w-full justify-center">Buy / Order</a>
                @if ($product->purchase_url)
                    <a href="{{ $product->purchase_url }}" target="_blank" rel="noopener" class="btn-secondary w-full justify-center">Instant Purchase Link</a>
                @endif
            </div>
        </aside>
    </div>
</section>

@if ($relatedProducts->isNotEmpty())
<section class="border-t border-warm-300/40 bg-warm-200/50 py-14 sm:py-20 dark:border-slate-700/40 dark:bg-navy-900/60">
    <div class="mx-auto max-w-7xl px-4 sm:px-8">
        <h2 class="font-display text-3xl text-brand-ink">Related Products</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($relatedProducts as $related)
                <a href="{{ route('shop.show', $related) }}" class="group overflow-hidden rounded-2xl border border-warm-300/50 bg-warm-100 shadow-sm transition hover:-translate-y-0.5 hover:shadow-card dark:border-slate-700/50 dark:bg-navy-800">
                    @if ($related->image_path)
                        <img src="{{ asset('storage/' . $related->image_path) }}" alt="{{ $related->title }}" class="h-40 w-full object-cover transition duration-300 group-hover:scale-105">
                    @endif
                    <div class="p-4">
                        <p class="line-clamp-2 font-semibold text-brand-ink">{{ $related->title }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-brand-primary">{{ $related->currency ?? 'USD' }} {{ number_format((float) ($related->price ?? 0), 2) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<div x-data="{ open:false, src:'' }" @open-gallery-image.window="open=true;src=$event.detail.src" x-show="open" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center bg-navy-900/75 p-4" @click.self="open=false" @keydown.escape.window="open=false">
    <button type="button" class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white" @click="open=false">×</button>
    <img :src="src" alt="Preview" class="max-h-[90vh] max-w-[90vw] rounded-xl object-contain shadow-2xl">
</div>

</x-public-layout>
