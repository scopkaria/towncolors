@props(['status'])

@php
    $map = [
        'pending'     => 'bg-warm-200 text-warm-700 border-warm-300/50',
        'assigned'    => 'bg-blue-50 text-blue-600 border-blue-200',
        'in_progress' => 'bg-accent-light text-brand-primary border-accent/30',
        'completed'   => 'bg-emerald-50 text-emerald-600 border-emerald-200',
    ];
    $labels = [
        'pending'     => 'Pending',
        'assigned'    => 'Assigned',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
    ];
    $classes = $map[$status] ?? $map['pending'];
    $label  = $labels[$status] ?? ucfirst($status);
@endphp

<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wider {{ $classes }}">
    {{ $label }}
</span>
