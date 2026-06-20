@props(['label', 'value', 'change' => null, 'icon' => null])

<div class="bg-white rounded-xl border border-slate-200 p-5">
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">{{ $label }}</p>
        @if ($icon)
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <x-icon :name="$icon" class="w-4 h-4" />
            </div>
        @endif
    </div>
    <p class="text-2xl font-semibold text-slate-900 mt-2 font-mono-num">{{ $value }}</p>
    @if (!is_null($change))
        <p class="text-xs mt-1 {{ $change >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
            {{ $change >= 0 ? '+' : '' }}{{ $change }}% vs previous period
        </p>
    @endif
</div>
