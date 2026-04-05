@props([
    'iconWrapClass' => 'flex h-10 w-10 items-center justify-center rounded-xl bg-slate-950 text-white shadow-card',
    'iconClass'     => 'h-6 w-6',
    'nameClass'     => 'font-display text-lg sm:text-xl text-brand-ink dark:text-slate-100',
    'logoClass'     => 'h-10 w-auto object-contain',
])

@php
    $logoUrl = \App\Models\Setting::instance()->logoUrl();
@endphp

@if ($logoUrl)
    <img src="{{ $logoUrl }}" class="{{ $logoClass }}" alt="{{ config('app.name') }}">
    @isset($subtitle)
        {{ $subtitle }}
    @endisset
@else
    <span class="{{ $iconWrapClass }}">
        <x-application-logo class="{{ $iconClass }}" />
    </span>
    <span>
        <span class="{{ $nameClass }}">{{ config('app.name') }}</span>
        @isset($subtitle)
            {{ $subtitle }}
        @endisset
    </span>
@endif
