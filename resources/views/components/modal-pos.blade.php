@props(['id', 'title' => null, 'maxWidth' => 'md'])

@php
    $widths = ['sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-lg', 'xl' => 'max-w-xl', '2xl' => 'max-w-2xl'];
    $widthClass = $widths[$maxWidth] ?? $widths['md'];
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="modal-backdrop fixed inset-0 bg-slate-900/50 transition-opacity" data-modal-close="{{ $id }}">
    </div>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="modal-panel w-full {{ $widthClass }} bg-white rounded-xl shadow-xl">
            @if ($title)
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                    <button type="button" data-modal-close="{{ $id }}"
                        class="text-slate-400 hover:text-slate-600">
                        <x-icon name="x" class="w-5 h-5" />
                    </button>
                </div>
            @endif

            <div class="px-5 py-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
