@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full rounded-2xl border border-warm-300/50 bg-warm-100 px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 placeholder:text-stone-400 focus:border-accent/30 focus:ring focus:ring-accent/20']) !!}>
