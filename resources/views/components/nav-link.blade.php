@props(['href', 'icon' => null, 'active' => false])

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' =>
            'flex items-center gap-3 rounded-md px-3 py-2 font-medium transition ' .
            ($active ? 'bg-white/10 text-white' : 'text-slate-400 hover:bg-white/5 hover:text-white'),
    ]) }}>
    @if ($icon)
        <x-icon :name="$icon" class="w-4 h-4 shrink-0" />
    @endif
    <span>{{ $slot }}</span>
</a>
