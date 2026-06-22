@extends('layouts.admin')

@section('page-title', 'Stock Movement History')
@section('breadcrumb', 'Inventory Audit Log')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                    <x-icon name="clock" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-primary">Stock Movement History</h2>
                    <div class="flex items-center gap-2 text-sm text-secondary">
                        <span>{{ $movements->total() }} total movements</span>
                        <span class="w-1 h-1 rounded-full bg-sage-300 dark:bg-sage-600 opacity-30"></span>
                        <span class="flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-sage-500 dark:bg-sage-400"></span>
                            {{ $movements->where('quantity', '>=', 0)->count() }} additions
                        </span>
                        <span class="w-1 h-1 rounded-full bg-sage-300 dark:bg-sage-600 opacity-30"></span>
                        <span class="flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            {{ $movements->where('quantity', '<', 0)->count() }} reductions
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Quick Stats --}}
                <div class="flex items-center gap-4 text-sm bg-card rounded-xl border border-theme px-4 py-2">
                    <div>
                        <span class="text-secondary">Total Changes</span>
                        <span class="font-bold text-primary font-mono-num ml-2">
                            {{ $movements->sum('quantity') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET"
            class="bg-card rounded-2xl border border-theme p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                        <x-icon name="warehouse" class="w-4 h-4" />
                    </div>
                    <select name="warehouse_id"
                        class="w-full rounded-xl border-theme pl-9 pr-10 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition appearance-none cursor-pointer">
                        <option value="">All warehouses</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected(($filters['warehouse_id'] ?? null) == $warehouse->id)>{{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary opacity-40 pointer-events-none">
                        <x-icon name="chevron-down" class="w-4 h-4" />
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                        <x-icon name="cube" class="w-4 h-4" />
                    </div>
                    <select name="type"
                        class="w-full rounded-xl border-theme pl-9 pr-10 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition appearance-none cursor-pointer">
                        <option value="">All movement types</option>
                        @foreach (\App\Enums\StockMovementType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(($filters['type'] ?? '') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary opacity-40 pointer-events-none">
                        <x-icon name="chevron-down" class="w-4 h-4" />
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                    <input type="text" name="product" value="{{ $filters['product'] ?? '' }}"
                        placeholder="Search product..."
                        class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-white text-sm font-medium px-4 py-2.5 transition shadow-sm hover:shadow-md">
                        <x-icon name="filter" class="w-4 h-4" />
                        Filter
                    </button>
                    @if (request()->hasAny(['warehouse_id', 'type', 'product']))
                        <a href="{{ route('admin.stock-movements.index') }}"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-theme text-sm font-medium px-4 py-2.5 text-secondary hover:bg-sage-50 dark:hover:bg-sage-900/20 hover:text-primary transition">
                            <x-icon name="refresh" class="w-4 h-4" />
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            @if ($movements->total() > 0)
                <div class="mt-3 pt-3 border-t border-theme flex items-center justify-between">
                    <span class="text-xs text-secondary">
                        <span class="font-medium text-primary">{{ $movements->total() }}</span> records found
                    </span>
                    <span class="text-xs text-secondary opacity-60">
                        Latest movements shown first
                    </span>
                </div>
            @endif
        </form>

        {{-- Movements Table --}}
        <div class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme text-sm">
                    <thead class="bg-sage-50 dark:bg-sage-900/20">
                        <tr>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clock" class="w-3.5 h-3.5" />
                                    Date &amp; Time
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="cube" class="w-3.5 h-3.5" />
                                    Product
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="warehouse" class="w-3.5 h-3.5" />
                                    Warehouse
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="tag" class="w-3.5 h-3.5" />
                                    Type
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="trending-up" class="w-3.5 h-3.5" />
                                    Change
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="inbox" class="w-3.5 h-3.5" />
                                    Balance
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="user" class="w-3.5 h-3.5" />
                                    By
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-center font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-center gap-1.5">
                                    <x-icon name="settings" class="w-3.5 h-3.5" />
                                    Details
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme">
                        @forelse ($movements as $movement)
                            <tr class="hover:bg-sage-50/50 dark:hover:bg-sage-900/20 transition group">
                                <td class="px-6 py-4">
                                    <div class="text-secondary text-xs">
                                        <div class="flex items-center gap-1.5">
                                            <x-icon name="calendar" class="w-3 h-3 text-secondary opacity-40" />
                                            {{ $movement->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="flex items-center gap-1.5 text-secondary opacity-60 mt-0.5">
                                            <x-icon name="clock" class="w-3 h-3" />
                                            {{ $movement->created_at->format('g:i A') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="min-w-0">
                                        <p class="font-medium text-primary">{{ $movement->product->name }}</p>
                                        <p class="text-xs text-secondary font-mono-num">{{ $movement->product->sku }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sage-100/50 dark:bg-sage-800/30 text-xs font-medium text-sage-700 dark:text-sage-300 border border-sage-200 dark:border-sage-700">
                                        <x-icon name="warehouse" class="w-3 h-3" />
                                        {{ $movement->warehouse->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5">
                                        <x-badge :color="$movement->quantity >= 0 ? 'success' : 'danger'">
                                            <span class="flex items-center gap-1.5">
                                                @if ($movement->quantity >= 0)
                                                    <x-icon name="plus" class="w-3 h-3" />
                                                @else
                                                    <x-icon name="minus" class="w-3 h-3" />
                                                @endif
                                                {{ $movement->type->label() }}
                                            </span>
                                        </x-badge>
                                        @if ($movement->is_from_offline_sync)
                                            <span title="Synced from an offline POS sale" class="flex-shrink-0">
                                                <x-icon name="exclamation" class="w-3.5 h-3.5 text-amber-500" />
                                            </span>
                                        @endif
                                        @if ($movement->reference_type)
                                            <span class="text-xs text-secondary opacity-60 font-mono-num">
                                                #{{ $movement->reference_id }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num font-semibold">
                                    <span
                                        class="@if ($movement->quantity >= 0) text-sage-600 dark:text-sage-400 @else text-red-600 dark:text-red-400 @endif">
                                        {{ $movement->quantity >= 0 ? '+' : '' }}{{ $movement->quantity }}
                                    </span>
                                    @if ($movement->quantity != 0)
                                        <div class="text-xs text-secondary opacity-60">
                                            {{ $movement->quantity >= 0 ? 'In' : 'Out' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num">
                                    <div class="text-primary font-medium">{{ $movement->quantity_after }}</div>
                                    <div class="text-xs text-secondary opacity-60">
                                        {{ $movement->quantity_before }} → {{ $movement->quantity_after }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center text-xs font-medium flex-shrink-0">
                                            {{ $movement->user ? substr($movement->user->name, 0, 1) : 'S' }}
                                        </div>
                                        <span
                                            class="text-secondary text-sm">{{ $movement->user->name ?? 'System' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($movement->reference_type && $movement->reference_id)
                                        @php
                                            $route = match ($movement->reference_type) {
                                                'App\Models\Purchase' => 'admin.purchases.show',
                                                'App\Models\Sale' => 'admin.sales.show',
                                                'App\Models\StockAdjustment' => 'admin.stock-adjustments.show',
                                                'App\Models\SaleRefund' => 'admin.sales.show',
                                                default => null,
                                            };
                                        @endphp
                                        @if ($route)
                                            <a href="{{ route($route, $movement->reference_id) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs text-secondary hover:bg-sage-100 dark:hover:bg-sage-800/30 hover:text-sage-700 dark:hover:text-sage-300 transition group"
                                                title="View reference">
                                                <x-icon name="eye" class="w-3.5 h-3.5" />
                                                {{-- <span class="opacity-0 group-hover:opacity-100 transition">View</span> --}}
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-xs text-secondary opacity-30">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-20 h-20 rounded-2xl bg-sage-100/30 dark:bg-sage-800/20 flex items-center justify-center mb-4">
                                            <x-icon name="clock" class="w-10 h-10 text-secondary opacity-30" />
                                        </div>
                                        <p class="text-lg font-medium text-primary">No stock movements found</p>
                                        <p class="text-sm text-secondary mt-1">
                                            @if (request()->hasAny(['warehouse_id', 'type', 'product']))
                                                Try adjusting your search filters
                                            @else
                                                Stock movements will appear here when inventory changes occur
                                            @endif
                                        </p>
                                        @can('stock-adjustments.create')
                                            <a href="{{ route('admin.stock-adjustments.create') }}"
                                                class="inline-flex items-center gap-2 mt-4 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:shadow-md transition-all duration-200">
                                                <x-icon name="plus" class="w-4 h-4" />
                                                Create Stock Adjustment
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movements->hasPages())
                <div class="border-t border-theme px-6 py-4 flex items-center justify-between">
                    <div class="text-sm text-secondary">
                        Showing <span class="font-medium text-primary">{{ $movements->firstItem() ?? 0 }}</span>
                        to <span class="font-medium text-primary">{{ $movements->lastItem() ?? 0 }}</span>
                        of <span class="font-medium text-primary">{{ $movements->total() }}</span> movements
                    </div>
                    <div>
                        {{ $movements->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Legend / Movement Types Summary --}}
        @if ($movements->isNotEmpty())
            <div class="bg-card rounded-2xl border border-theme p-4 shadow-sm">
                <div class="flex flex-wrap items-center gap-4">
                    <span class="text-xs font-medium text-secondary uppercase tracking-wider">Movement Types:</span>
                    @foreach (\App\Enums\StockMovementType::cases() as $type)
                        @php
                            $count = $movements->where('type', $type)->count();
                            $total = $movements->where('type', $type)->sum('quantity');
                        @endphp
                        @if ($count > 0)
                            <div class="flex items-center gap-2 text-xs">
                                <x-badge :color="$movement->quantity >= 0 ? 'success' : 'danger'" class="text-[10px]">
                                    {{ $type->label() }}
                                </x-badge>
                                <span class="text-secondary">
                                    {{ $count }} movements
                                    <span
                                        class="font-mono-num font-medium text-primary">({{ $total >= 0 ? '+' : '' }}{{ $total }})</span>
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
