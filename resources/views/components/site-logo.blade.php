@props([
    'iconWrapClass' => 'flex h-10 w-10 items-center justify-center rounded-xl bg-navy-800 text-white shadow-card',
    'iconClass'     => 'h-6 w-6',
    'nameClass'     => 'font-display text-lg sm:text-xl text-brand-ink dark:text-slate-100',
    'logoClass'     => 'h-10 w-auto object-contain',
])

@php
    $logoUrl = \App\Models\Setting::instance()->logoUrl();
@endphp

@php
    $settings = \App\Models\Setting::instance();
    $lightModeLogoUrl = $settings->themedLogoUrl(false);
    $darkModeLogoUrl = $settings->themedLogoUrl(true);
@endphp

@if ($lightModeLogoUrl || $darkModeLogoUrl || $logoUrl)
    @if ($lightModeLogoUrl || $darkModeLogoUrl)
        <img src="{{ $lightModeLogoUrl ?? $logoUrl ?? $darkModeLogoUrl }}" class="{{ $logoClass }} block dark:hidden" alt="{{ config('app.name') }}">
        <img src="{{ $darkModeLogoUrl ?? $logoUrl ?? $lightModeLogoUrl }}" class="{{ $logoClass }} hidden dark:block" alt="{{ config('app.name') }}">
    @else
        <img src="{{ $logoUrl }}" class="{{ $logoClass }}" alt="{{ config('app.name') }}">
    @endif
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
