@props(['color' => 'blue'])

@php
    $colors = [
        'blue' =>
            'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-200 dark:bg-blue-500/15 dark:text-blue-300 dark:ring-blue-500/30',

        'green' =>
            'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30',

        'success' =>
            'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30',

        'warning' =>
            'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30',

        'danger' =>
            'bg-red-50 text-red-700 ring-1 ring-inset ring-red-200 dark:bg-red-500/15 dark:text-red-300 dark:ring-red-500/30',

        'purple' =>
            'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-200 dark:bg-purple-500/15 dark:text-purple-300 dark:ring-purple-500/30',

        'gray' =>
            'bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200 dark:bg-slate-700/30 dark:text-slate-300 dark:ring-slate-600',
    ];
@endphp

<span
    {{ $attributes->merge([
        'class' =>
            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium tracking-wide whitespace-nowrap transition-colors ' .
            ($colors[$color] ?? $colors['blue']),
    ]) }}>
    {{ $slot }}
</span>
