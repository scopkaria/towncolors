@props([
    'badge' => null,
    'title' => '',
    'subtitle' => null,
    'media' => null,
    'heightClass' => 'min-h-[520px] sm:min-h-[620px] lg:min-h-[720px]',
])

@php
    $isVideo = $media && preg_match('/\.(mp4|webm|ogg)$/i', (string) $media);
@endphp

<section class="relative isolate overflow-hidden border-b border-warm-300/40">
    <div class="absolute inset-0">
        @if ($media)
            @if ($isVideo)
                <video class="h-full w-full object-cover" autoplay muted loop playsinline>
                    <source src="{{ $media }}">
                </video>
            @else
                <img src="{{ $media }}" alt="Hero background" class="h-full w-full object-cover">
            @endif
            <div class="absolute inset-0 bg-navy-900/65"></div>
        @else
            <div class="absolute inset-0 bg-transparent"></div>
        @endif
    </div>

    <div class="relative mx-auto flex {{ $heightClass }} max-w-7xl items-center px-4 py-16 text-center sm:px-8">
        <div class="mx-auto max-w-4xl">
            @if ($badge)
                <span class="inline-flex rounded-full border border-accent/30 bg-accent-light px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.25em] text-brand-primary">
                    {{ $badge }}
                </span>
            @endif
            <h1 class="mt-5 font-display text-3xl font-bold leading-tight {{ $media ? 'text-white' : 'text-brand-ink' }} sm:text-5xl lg:text-6xl">
                {{ $title }}
            </h1>
            @if ($subtitle)
                <p class="mx-auto mt-5 max-w-3xl text-base leading-8 {{ $media ? 'text-white/85' : 'text-brand-muted' }} sm:text-lg">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>
</section>
