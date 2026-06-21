@props(['href', 'icon' => null, 'active' => false])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' =>
            'flex items-center gap-3 rounded-md px-3 py-2 font-medium transition-smooth ' .
            ($active ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white'),
    ]) }}>
    @if ($icon)
        <x-icon :name="$icon" class="w-4 h-4 shrink-0" />
    @endif
    <span>{{ $slot }}</span>
    @if ($active)
        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
    @endif
</a>
