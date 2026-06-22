@extends('layouts.admin')

@section('page-title', 'Stock Adjustments')
@section('breadcrumb', 'Inventory Management')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                    <x-icon name="adjustments" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-primary">Stock Adjustments</h2>
                    <p class="text-sm text-secondary mt-0.5">Manual corrections for damage, loss, theft, or recounts.</p>
                </div>
            </div>
            @can('stock-adjustments.create')
                <a href="{{ route('admin.stock-adjustments.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:shadow-md transition-all duration-200 group">
                    <x-icon name="plus" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" />
                    New Adjustment
                </a>
            @endcan
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Adjustments</p>
                <p class="text-lg font-bold text-primary mt-1">{{ $adjustments->total() }}</p>
            </div>
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Pending</p>
                <p class="text-lg font-bold text-amber-600 dark:text-amber-400 mt-1">
                    {{ $adjustments->filter(fn($a) => !$a->isApproved())->count() }}</p>
            </div>
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Approved</p>
                <p class="text-lg font-bold text-sage-600 dark:text-sage-400 mt-1">
                    {{ $adjustments->filter(fn($a) => $a->isApproved())->count() }}</p>
            </div>
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">This Month</p>
                <p class="text-lg font-bold text-primary mt-1">
                    {{ $adjustments->filter(fn($a) => $a->created_at->isCurrentMonth())->count() }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="bg-card rounded-2xl border border-theme p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                        <x-icon name="search" class="w-4 h-4" />
                    </div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                        placeholder="Search adjustment #..."
                        class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition">
                </div>

                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                        <x-icon name="check-circle" class="w-4 h-4" />
                    </div>
                    <select name="status"
                        class="w-full rounded-xl border-theme pl-9 pr-10 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition appearance-none cursor-pointer">
                        <option value="">All statuses</option>
                        <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending</option>
                        <option value="approved" @selected(($filters['status'] ?? '') === 'approved')>Approved</option>
                    </select>
                    {{-- <div class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary opacity-40 pointer-events-none">
                        <x-icon name="chevron-down" class="w-4 h-4" />
                    </div> --}}
                </div>

                <div class="relative">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                        <x-icon name="warehouse" class="w-4 h-4" />
                    </div>
                    <select name="warehouse_id"
                        class="w-full rounded-xl border-theme pl-9 pr-10 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition appearance-none cursor-pointer">
                        <option value="">All warehouses</option>
                        @foreach ($warehouses ?? [] as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected(($filters['warehouse_id'] ?? null) == $warehouse->id)>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                    {{-- <div class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary opacity-40 pointer-events-none">
                        <x-icon name="chevron-down" class="w-4 h-4" />
                    </div> --}}
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-white text-sm font-medium px-4 py-2.5 transition shadow-sm hover:shadow-md">
                        <x-icon name="filter" class="w-4 h-4" />
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'status', 'warehouse_id']))
                        <a href="{{ route('admin.stock-adjustments.index') }}"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-theme text-sm font-medium px-4 py-2.5 text-secondary hover:bg-sage-50 dark:hover:bg-sage-900/20 hover:text-primary transition">
                            <x-icon name="refresh" class="w-4 h-4" />
                            Reset
                        </a>
                    @endif
                </div>
            </div>

            @if ($adjustments->total() > 0)
                <div class="mt-3 pt-3 border-t border-theme flex items-center justify-between">
                    <span class="text-xs text-secondary">
                        <span class="font-medium text-primary">{{ $adjustments->total() }}</span> adjustments found
                    </span>
                    <span class="text-xs text-secondary opacity-60">
                        Latest adjustments shown first
                    </span>
                </div>
            @endif
        </form>

        {{-- Adjustments Table --}}
        <div class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme text-sm">
                    <thead class="bg-sage-50 dark:bg-sage-900/20">
                        <tr>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="adjustments" class="w-3.5 h-3.5" />
                                    Adjustment #
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
                                    <x-icon name="info" class="w-3.5 h-3.5" />
                                    Reason
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="user" class="w-3.5 h-3.5" />
                                    Created By
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                    Status
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="calendar" class="w-3.5 h-3.5" />
                                    Date
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="settings" class="w-3.5 h-3.5" />
                                    Actions
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme">
                        @forelse ($adjustments as $adjustment)
                            <tr class="hover:bg-sage-50/50 dark:hover:bg-sage-900/20 transition group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-sage-100/50 dark:bg-sage-800/30 flex items-center justify-center flex-shrink-0">
                                            <x-icon name="adjustments" class="w-3.5 h-3.5 text-sage-600 dark:text-sage-400" />
                                        </div>
                                        <span
                                            class="font-mono-num font-semibold text-sage-600 dark:text-sage-400">{{ $adjustment->adjustment_number }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sage-100/50 dark:bg-sage-800/30 text-xs font-medium text-sage-700 dark:text-sage-300 border border-sage-200 dark:border-sage-700">
                                        <x-icon name="warehouse" class="w-3 h-3" />
                                        {{ $adjustment->warehouse->name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-secondary">
                                    {{ ucwords(str_replace('_', ' ', $adjustment->reason)) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-full bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center text-xs font-medium flex-shrink-0">
                                            {{ substr($adjustment->user->name, 0, 1) }}
                                        </div>
                                        <span class="text-secondary">{{ $adjustment->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <x-badge :color="$adjustment->isApproved() ? 'success' : 'warning'">
                                        <span class="flex items-center gap-1.5">
                                            @if ($adjustment->isApproved())
                                                <span class="w-1.5 h-1.5 rounded-full bg-sage-500 dark:bg-sage-400 animate-pulse"></span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            @endif
                                            {{ $adjustment->isApproved() ? 'Approved' : 'Pending' }}
                                        </span>
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 text-secondary text-sm">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-secondary opacity-40" />
                                        {{ $adjustment->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('admin.stock-adjustments.show', $adjustment) }}"
                                            class="p-1.5 rounded-lg text-secondary hover:bg-sage-100 dark:hover:bg-sage-800/30 hover:text-sage-700 dark:hover:text-sage-300 transition"
                                            title="View Details">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        @if (!$adjustment->isApproved())
                                            <button type="button" data-modal-target="approve-{{ $adjustment->id }}"
                                                class="p-1.5 rounded-lg text-secondary hover:bg-sage-100 dark:hover:bg-sage-800/30 hover:text-sage-700 dark:hover:text-sage-300 transition"
                                                title="Approve">
                                                <x-icon name="check-circle" class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Approve Modal --}}
                            @if (!$adjustment->isApproved())
                                <x-modal id="approve-{{ $adjustment->id }}" title="Approve Adjustment"
                                    description="Confirm this stock adjustment" icon="success">
                                    <form method="POST" action="{{ route('admin.stock-adjustments.approve', $adjustment) }}">
                                        @csrf
                                        <div class="space-y-4">
                                            <div
                                                class="flex items-start gap-4 p-4 bg-sage-50/50 dark:bg-sage-900/20 rounded-xl border border-theme">
                                                <div
                                                    class="flex-shrink-0 w-10 h-10 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                                                    <x-icon name="check-circle" class="w-5 h-5" />
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-primary">
                                                        Approve <strong>{{ $adjustment->adjustment_number }}</strong>?
                                                    </p>
                                                    <p class="text-xs text-secondary mt-1">
                                                        This will apply the stock changes to the warehouse inventory.
                                                        {{ $adjustment->items->count() }} items will be adjusted.
                                                    </p>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-secondary mb-1.5">
                                                    Notes (Optional)
                                                </label>
                                                <div class="relative">
                                                    <div class="absolute left-3 top-3 text-secondary opacity-40">
                                                        <x-icon name="info" class="w-4 h-4" />
                                                    </div>
                                                    <textarea name="approval_notes" rows="2" placeholder="Add any notes about this approval..."
                                                        class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition resize-none"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex justify-end gap-2">
                                            <button type="button" data-modal-close="approve-{{ $adjustment->id }}"
                                                class="rounded-xl border border-theme text-sm font-medium px-5 py-2 text-secondary hover:bg-sage-50 dark:hover:bg-sage-900/20 hover:text-primary transition">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                                                <x-icon name="check-circle" class="w-4 h-4" />
                                                Approve Adjustment
                                            </button>
                                        </div>
                                    </form>
                                </x-modal>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-20 h-20 rounded-2xl bg-sage-100/30 dark:bg-sage-800/20 flex items-center justify-center mb-4">
                                            <x-icon name="adjustments" class="w-10 h-10 text-secondary opacity-30" />
                                        </div>
                                        <p class="text-lg font-medium text-primary">No stock adjustments found</p>
                                        <p class="text-sm text-secondary mt-1">
                                            @if (request()->hasAny(['search', 'status', 'warehouse_id']))
                                                Try adjusting your search filters
                                            @else
                                                Start by creating your first stock adjustment
                                            @endif
                                        </p>
                                        @can('stock-adjustments.create')
                                            <a href="{{ route('admin.stock-adjustments.create') }}"
                                                class="inline-flex items-center gap-2 mt-4 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:shadow-md transition-all duration-200">
                                                <x-icon name="plus" class="w-4 h-4" />
                                                New Adjustment
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($adjustments->hasPages())
                <div class="border-t border-theme px-6 py-4 flex items-center justify-between">
                    <div class="text-sm text-secondary">
                        Showing <span class="font-medium text-primary">{{ $adjustments->firstItem() ?? 0 }}</span>
                        to <span class="font-medium text-primary">{{ $adjustments->lastItem() ?? 0 }}</span>
                        of <span class="font-medium text-primary">{{ $adjustments->total() }}</span> adjustments
                    </div>
                    <div>
                        {{ $adjustments->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
