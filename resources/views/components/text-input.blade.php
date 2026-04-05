@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm text-brand-ink shadow-sm transition duration-200 placeholder:text-stone-400 focus:border-orange-200 focus:ring focus:ring-orange-100']) !!}>
