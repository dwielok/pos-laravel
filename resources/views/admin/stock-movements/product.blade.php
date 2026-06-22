@extends('layouts.admin')

@section('page-title', 'Stock History: ' . $product->name)
@section('breadcrumb', 'Inventory Audit')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.index') }}"
                    class="text-sm text-secondary hover:text-sage-600 dark:hover:text-sage-400 transition flex items-center gap-1.5 group">
                    <x-icon name="chevron-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" />
                    Back to Products
                </a>
            </div>
            <div class="flex items-center gap-3">
                {{-- Quick Stats --}}
                <div class="flex items-center gap-4 text-sm bg-card rounded-xl border border-theme px-4 py-2">
                    <div>
                        <span class="text-secondary">Total Movements</span>
                        <span class="font-bold text-primary font-mono-num ml-2">{{ $movements->total() }}</span>
                    </div>
                    <div>
                        <span class="text-secondary">Net Change</span>
                        <span class="font-bold text-primary font-mono-num ml-2">
                            {{ $movements->sum('quantity') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Summary Card --}}
        <div class="bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex items-center gap-4">
                    @if ($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}"
                            class="w-16 h-16 rounded-xl object-cover border border-theme">
                    @else
                        <div class="w-16 h-16 rounded-xl bg-sage-100/50 dark:bg-sage-800/30 flex items-center justify-center text-sage-400 border border-theme">
                            <x-icon name="photo" class="w-8 h-8" />
                        </div>
                    @endif
                    <div>
                        <h2 class="text-xl font-semibold text-primary">{{ $product->name }}</h2>
                        <div class="flex items-center gap-2 text-sm text-secondary mt-0.5">
                            <span class="font-mono-num">{{ $product->sku }}</span>
                            <span class="w-1 h-1 rounded-full bg-sage-300 dark:bg-sage-600 opacity-30"></span>
                            <span>{{ $product->category->name ?? 'Uncategorized' }}</span>
                            @if ($product->unit)
                                <span class="w-1 h-1 rounded-full bg-sage-300 dark:bg-sage-600 opacity-30"></span>
                                <span>{{ $product->unit->symbol }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Stock (all warehouses)</p>
                    <p class="text-3xl font-bold font-mono-num
                        @if ($product->isLowStock()) text-amber-600 dark:text-amber-400
                        @elseif($product->totalStock() == 0) text-red-600 dark:text-red-400
                        @else text-sage-700 dark:text-sage-300 @endif">
                        {{ $product->totalStock() }}
                    </p>
                    @if ($product->isLowStock())
                        <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">⚠️ Low stock</p>
                    @elseif($product->totalStock() == 0)
                        <p class="text-xs text-red-600 dark:text-red-400 font-medium">❌ Out of stock</p>
                    @else
                        <p class="text-xs text-sage-500 font-medium">✓ In stock</p>
                    @endif
                </div>
            </div>

            {{-- Additional Product Info --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 pt-4 border-t border-theme">
                <div>
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Cost Price</p>
                    <p class="font-mono-num text-primary mt-1">{{ $product->costPrice()->formatted() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Selling Price</p>
                    <p class="font-mono-num text-primary mt-1">{{ $product->sellingPrice()->formatted() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Min Stock Level</p>
                    <p class="font-mono-num text-primary mt-1">{{ $product->min_stock_level ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Status</p>
                    <x-badge :color="$product->status === 'active' ? 'success' : 'gray'" class="mt-1">
                        {{ ucfirst($product->status) }}
                    </x-badge>
                </div>
            </div>
        </div>

        {{-- Movements Table --}}
        <div class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="px-6 py-4 border-b border-theme flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                        <x-icon name="clock" class="w-4 h-4" />
                    </div>
                    <h3 class="font-semibold text-primary">Movement History</h3>
                    <span class="text-xs text-secondary bg-sage-100/50 dark:bg-sage-800/30 px-2.5 py-1 rounded-full border border-sage-200 dark:border-sage-700">
                        {{ $movements->total() }} movements
                    </span>
                </div>
                <div class="flex items-center gap-4 text-xs text-secondary">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-sage-500 dark:bg-sage-400"></span>
                        Additions
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Reductions
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme text-sm">
                    <thead class="bg-sage-50 dark:bg-sage-900/20">
                        <tr>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="calendar" class="w-3.5 h-3.5" />
                                    Date
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
                                    <x-icon name="info" class="w-3.5 h-3.5" />
                                    Reference
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
                                <td class="px-6 py-4 text-secondary text-xs whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <x-icon name="clock" class="w-3 h-3 text-secondary opacity-40" />
                                        {{ $movement->created_at->format('M d, Y g:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sage-100/50 dark:bg-sage-800/30 text-xs font-medium text-sage-700 dark:text-sage-300 border border-sage-200 dark:border-sage-700">
                                        <x-icon name="warehouse" class="w-3 h-3" />
                                        {{ $movement->warehouse->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
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
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num font-semibold
                                    @if ($movement->quantity >= 0) text-sage-600 dark:text-sage-400
                                    @else text-red-600 dark:text-red-400 @endif">
                                    {{ $movement->quantity >= 0 ? '+' : '' }}{{ $movement->quantity }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num text-primary font-medium">
                                    {{ $movement->quantity_after }}
                                    <div class="text-xs text-secondary opacity-60 font-normal">
                                        {{ $movement->quantity_before }} → {{ $movement->quantity_after }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-secondary text-sm">
                                    @if ($movement->reference_type && $movement->reference_id)
                                        <span class="font-mono-num text-xs">#{{ $movement->reference_id }}</span>
                                        <span class="text-xs opacity-60">{{ class_basename($movement->reference_type) }}</span>
                                    @else
                                        <span class="text-xs text-secondary opacity-40">—</span>
                                    @endif
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
                                                <span class="opacity-0 group-hover:opacity-100 transition">View</span>
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-xs text-secondary opacity-30">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 rounded-2xl bg-sage-100/30 dark:bg-sage-800/20 flex items-center justify-center mb-4">
                                            <x-icon name="clock" class="w-10 h-10 text-secondary opacity-30" />
                                        </div>
                                        <p class="text-lg font-medium text-primary">No movements recorded</p>
                                        <p class="text-sm text-secondary mt-1">This product has no stock movement history yet</p>
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

        {{-- Summary Stats --}}
        @if ($movements->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Additions</p>
                    <p class="text-lg font-bold text-sage-600 dark:text-sage-400 mt-1 font-mono-num">
                        {{ $movements->where('quantity', '>=', 0)->sum('quantity') }}
                    </p>
                </div>
                <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Reductions</p>
                    <p class="text-lg font-bold text-red-600 dark:text-red-400 mt-1 font-mono-num">
                        {{ abs($movements->where('quantity', '<', 0)->sum('quantity')) }}
                    </p>
                </div>
                <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Net Change</p>
                    <p class="text-lg font-bold text-primary mt-1 font-mono-num">
                        {{ $movements->sum('quantity') }}
                    </p>
                </div>
                <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Most Recent</p>
                    <p class="text-sm font-medium text-primary mt-1">
                        {{ $movements->first()->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @endif
    </div>
@endsection
