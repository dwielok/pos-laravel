@props(['href', 'icon' => null, 'active' => false])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' =>
            'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 ' .
            ($active
                ? 'bg-sage-100 text-sage-700 shadow-sm'
                : 'text-sage-600 hover:bg-sage-50 hover:text-sage-800'),
    ]) }}>
    @if ($icon)
        <x-icon :name="$icon" class="w-4 h-4 shrink-0" :class="$active ? 'text-sage-600' : 'text-sage-400 group-hover:text-sage-600'" />
    @endif
    <span>{{ $slot }}</span>
    @if ($active)
        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-sage-400 shadow-sm shadow-sage-200"></span>
    @endif
</a>

<style>
    /* Ensure sage color variables are available */
    .bg-sage-50 { background-color: var(--sage-50, #f6f8f4); }
    .bg-sage-100 { background-color: var(--sage-100, #e8ede3); }
    .text-sage-400 { color: var(--sage-400, #94b387); }
    .text-sage-600 { color: var(--sage-600, #5e7e51); }
    .text-sage-700 { color: var(--sage-700, #47623d); }
    .text-sage-800 { color: var(--sage-800, #32482b); }
    .hover\:bg-sage-50:hover { background-color: var(--sage-50, #f6f8f4); }
    .hover\:text-sage-800:hover { color: var(--sage-800, #32482b); }
    .shadow-sage-200 { --tw-shadow-color: var(--sage-200, #d0dec9); }
    .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03), 0 1px 3px 0 rgba(0, 0, 0, 0.02); }
</style>
