@props(['label', 'value', 'change' => null, 'icon' => null, 'warning' => false])

<div
    class="bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-0.5 group">
    <div class="flex items-center justify-between">
        <p class="text-sm font-medium text-secondary">{{ $label }}</p>
        @if ($icon)
            <div
                class="w-10 h-10 rounded-xl flex items-center justify-center
    {{ $warning
        ? 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 dark:bg-amber-500/15 dark:text-amber-300 dark:ring-amber-500/30'
        : 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-500/30' }}
    transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                <x-icon :name="$icon" class="w-4.5 h-4.5" />
            </div>
        @endif
    </div>
    <p class="text-2xl font-bold text-primary mt-2 font-mono-num tracking-tight">{{ $value }}</p>
    @if (!is_null($change))
        <div class="flex items-center gap-1.5 mt-1">
            <span
                class="inline-flex items-center gap-0.5 text-xs {{ $change >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                @if ($change >= 0)
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                @else
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                @endif
                {{ abs($change) }}%
            </span>
            <span class="text-xs text-secondary opacity-60">vs previous period</span>
        </div>
    @endif
</div>
