@props(['color' => 'sage'])

@php
    $colors = [
        'sage' =>
            'bg-sage-100 text-sage-700 ring-1 ring-inset ring-sage-200 dark:bg-sage-500/15 dark:text-sage-300 dark:ring-sage-500/30',

        'green' =>
            'bg-sage-100 text-sage-700 ring-1 ring-inset ring-sage-200 dark:bg-sage-500/15 dark:text-sage-300 dark:ring-sage-500/30',

        'success' =>
            'bg-sage-100 text-sage-700 ring-1 ring-inset ring-sage-200 dark:bg-sage-500/15 dark:text-sage-300 dark:ring-sage-500/30',

        'warning' =>
            'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30',

        'danger' =>
            'bg-red-50 text-red-700 ring-1 ring-inset ring-red-200 dark:bg-red-500/15 dark:text-red-300 dark:ring-red-500/30',

        'info' =>
            'bg-sage-50 text-sage-600 ring-1 ring-inset ring-sage-200 dark:bg-sage-500/15 dark:text-sage-300 dark:ring-sage-500/30',

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
            ($colors[$color] ?? $colors['sage']),
    ]) }}>
    {{ $slot }}
</span>

<style>
    /* Sage color utilities for the badge */
    .bg-sage-50 { background-color: var(--sage-50, #f6f8f4); }
    .bg-sage-100 { background-color: var(--sage-100, #e8ede3); }
    .text-sage-600 { color: var(--sage-600, #5e7e51); }
    .text-sage-700 { color: var(--sage-700, #47623d); }
    .ring-sage-200 { --tw-ring-color: var(--sage-200, #d0dec9); }
    .ring-sage-500 { --tw-ring-color: var(--sage-500, #779b68); }
    .dark\:bg-sage-500\/15 { background-color: rgba(119, 155, 104, 0.15); }
    .dark\:text-sage-300 { color: var(--sage-300, #b3c9a8); }
    .dark\:ring-sage-500\/30 { --tw-ring-color: rgba(119, 155, 104, 0.3); }
</style>
