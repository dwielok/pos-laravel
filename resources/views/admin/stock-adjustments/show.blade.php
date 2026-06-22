@extends('layouts.admin')

@section('page-title', $adjustment->adjustment_number)
@section('breadcrumb', 'Stock Adjustment Details')

@section('content')
    <div class="space-y-6">
        {{-- Header Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <a href="{{ route('admin.stock-adjustments.index') }}"
                class="text-sm text-secondary hover:text-sage-600 dark:hover:text-sage-400 transition flex items-center gap-1.5 group">
                <x-icon name="chevron-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" />
                Back to Adjustments
            </a>

            @can('stock-adjustments.approve')
                @if (!$adjustment->isApproved())
                    <button type="button" data-modal-target="approve-adjustment"
                        class="inline-flex items-center gap-2 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2.5 text-white shadow-sm hover:shadow-md transition">
                        <x-icon name="check-circle" class="w-4 h-4" />
                        Approve & Apply to Stock
                    </button>
                @endif
            @endcan
        </div>

        {{-- Adjustment Summary Card --}}
        <div class="bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                            <x-icon name="adjustments" class="w-6 h-6" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-primary font-mono-num">{{ $adjustment->adjustment_number }}</h2>
                            <div class="flex flex-wrap items-center gap-2 text-sm text-secondary mt-0.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="warehouse" class="w-3.5 h-3.5" />
                                    {{ $adjustment->warehouse->name }}
                                </span>
                                <span class="w-1 h-1 rounded-full bg-sage-300 dark:bg-sage-600 opacity-30"></span>
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="info" class="w-3.5 h-3.5" />
                                    {{ ucwords(str_replace('_', ' ', $adjustment->reason)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <x-badge :color="$adjustment->isApproved() ? 'success' : 'warning'" class="text-sm px-4 py-1.5">
                        <span class="flex items-center gap-1.5">
                            @if ($adjustment->isApproved())
                                <span class="w-1.5 h-1.5 rounded-full bg-sage-500 dark:bg-sage-400 animate-pulse"></span>
                            @else
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            @endif
                            {{ $adjustment->isApproved() ? 'Approved' : 'Pending Approval' }}
                        </span>
                    </x-badge>
                    @if (!$adjustment->isApproved())
                        <span class="text-xs text-secondary bg-sage-100/50 dark:bg-sage-800/30 px-2.5 py-1 rounded-full border border-sage-200 dark:border-sage-700">
                            <x-icon name="clock" class="w-3 h-3 inline mr-1" />
                            Awaiting Review
                        </span>
                    @endif
                </div>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-theme">
                <div class="bg-sage-50/50 dark:bg-sage-900/20 rounded-xl p-3">
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Created By</p>
                    <p class="font-medium text-primary mt-1 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ substr($adjustment->user->name, 0, 1) }}
                        </span>
                        {{ $adjustment->user->name }}
                    </p>
                    <p class="text-xs text-secondary mt-0.5">{{ $adjustment->created_at->format('M d, Y g:i A') }}</p>
                </div>
                @if ($adjustment->isApproved())
                    <div class="bg-sage-50/50 dark:bg-sage-900/20 rounded-xl p-3">
                        <p class="text-xs font-medium text-secondary uppercase tracking-wider">Approved By</p>
                        <p class="font-medium text-primary mt-1 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ substr($adjustment->approver->name, 0, 1) }}
                            </span>
                            {{ $adjustment->approver->name }}
                        </p>
                        <p class="text-xs text-secondary mt-0.5">{{ $adjustment->approved_at->format('M d, Y g:i A') }}</p>
                    </div>
                @else
                    <div class="bg-sage-50/50 dark:bg-sage-900/20 rounded-xl p-3">
                        <p class="text-xs font-medium text-secondary uppercase tracking-wider">Status</p>
                        <p class="font-medium text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-2">
                            <x-icon name="clock" class="w-4 h-4" />
                            Pending Approval
                        </p>
                    </div>
                @endif
                <div class="bg-sage-50/50 dark:bg-sage-900/20 rounded-xl p-3">
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Items</p>
                    <p class="font-medium text-primary mt-1 flex items-center gap-2">
                        <x-icon name="cube" class="w-4 h-4 text-secondary opacity-50" />
                        {{ $adjustment->items->count() }} products
                    </p>
                </div>
                <div class="bg-sage-50/50 dark:bg-sage-900/20 rounded-xl p-3">
                    <p class="text-xs font-medium text-secondary uppercase tracking-wider">Net Adjustment</p>
                    @php
                        $totalDiff = $adjustment->items->sum('difference');
                    @endphp
                    <p class="font-medium mt-1 flex items-center gap-2
                        @if ($totalDiff > 0) text-sage-600 dark:text-sage-400
                        @elseif($totalDiff < 0) text-red-600
                        @else text-secondary @endif">
                        <x-icon name="adjustments" class="w-4 h-4" />
                        {{ $totalDiff > 0 ? '+' : '' }}{{ $totalDiff }} units
                    </p>
                </div>
            </div>

            {{-- Notes --}}
            @if ($adjustment->notes)
                <div class="mt-4 p-4 bg-sage-50/50 dark:bg-sage-900/20 rounded-xl border border-theme">
                    <div class="flex items-start gap-2">
                        <x-icon name="info" class="w-4 h-4 text-secondary opacity-50 mt-0.5" />
                        <div>
                            <p class="text-xs font-medium text-secondary uppercase tracking-wider">Notes</p>
                            <p class="text-sm text-primary mt-1">{{ $adjustment->notes }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Approval Warning --}}
            @if (!$adjustment->isApproved())
                <div class="mt-4 flex items-start gap-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 px-4 py-3 text-sm text-amber-800 dark:text-amber-200">
                    <x-icon name="exclamation" class="w-5 h-5 shrink-0 mt-0.5" />
                    <span>Stock levels have <strong>not</strong> been changed yet. Approve this adjustment to apply the changes below.</span>
                </div>
            @endif
        </div>

        {{-- Items Table --}}
        <div class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="px-6 py-4 border-b border-theme flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                        <x-icon name="cube" class="w-4 h-4" />
                    </div>
                    <h3 class="font-semibold text-primary">Adjustment Items</h3>
                    <span class="text-xs text-secondary bg-sage-100/50 dark:bg-sage-800/30 px-2.5 py-1 rounded-full border border-sage-200 dark:border-sage-700">
                        {{ $adjustment->items->count() }} items
                    </span>
                </div>
                <div class="flex items-center gap-4 text-xs text-secondary">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-sage-500 dark:bg-sage-400"></span>
                        System Quantity
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Counted Quantity
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Difference
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme text-sm">
                    <thead class="bg-sage-50 dark:bg-sage-900/20">
                        <tr>
                            <th class="px-6 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="tag" class="w-3.5 h-3.5" />
                                    Product
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="inbox" class="w-3.5 h-3.5" />
                                    System Qty
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="clipboard-list" class="w-3.5 h-3.5" />
                                    Counted Qty
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="adjustments" class="w-3.5 h-3.5" />
                                    Difference
                                </span>
                            </th>
                            <th class="px-6 py-3.5 text-center font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-center gap-1.5">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                    Status
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme">
                        @foreach ($adjustment->items as $item)
                            @php
                                $isPositive = $item->difference > 0;
                                $isNegative = $item->difference < 0;
                            @endphp
                            <tr class="hover:bg-sage-50/50 dark:hover:bg-sage-900/20 transition">
                                <td class="px-6 py-4">
                                    <div class="min-w-0">
                                        <p class="font-medium text-primary">{{ $item->product->name }}</p>
                                        <p class="text-xs text-secondary font-mono-num">{{ $item->product->sku }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num text-secondary">
                                    {{ $item->system_quantity }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num text-primary">
                                    {{ $item->counted_quantity }}
                                </td>
                                <td class="px-6 py-4 text-right font-mono-num font-medium
                                    @if ($isPositive) text-sage-600 dark:text-sage-400
                                    @elseif($isNegative) text-red-600
                                    @else text-secondary @endif">
                                    {{ $item->difference > 0 ? '+' : '' }}{{ $item->difference }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($isPositive)
                                        <x-badge color="success" class="text-[10px]">Increase</x-badge>
                                    @elseif($isNegative)
                                        <x-badge color="danger" class="text-[10px]">Decrease</x-badge>
                                    @else
                                        <x-badge color="gray" class="text-[10px]">No Change</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-sage-50/50 dark:bg-sage-900/20">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-secondary font-medium">Net Adjustment</td>
                            <td class="px-6 py-3 text-right font-mono-num font-bold
                                @if ($totalDiff > 0) text-sage-600 dark:text-sage-400
                                @elseif($totalDiff < 0) text-red-600
                                @else text-secondary @endif">
                                {{ $totalDiff > 0 ? '+' : '' }}{{ $totalDiff }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Related Actions --}}
        {{-- <div class="flex flex-wrap gap-3 justify-end">
            @can('stock-adjustments.approve')
                @if (!$adjustment->isApproved())
                    <button type="button" data-modal-target="approve-adjustment"
                        class="inline-flex items-center gap-2 rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-5 py-2.5 text-white shadow-sm hover:shadow-md transition">
                        <x-icon name="check-circle" class="w-4 h-4" />
                        Approve & Apply to Stock
                    </button>
                @endif
            @endcan
        </div> --}}
    </div>

    {{-- Approve Modal --}}
    <x-modal id="approve-adjustment" title="Approve Adjustment" description="Apply stock changes to inventory" icon="success" maxWidth="lg">
        <form method="POST" action="{{ route('admin.stock-adjustments.approve', $adjustment) }}">
            @csrf
            <div class="space-y-4">
                <div class="flex items-start gap-4 p-4 bg-sage-50/50 dark:bg-sage-900/20 rounded-xl border border-theme">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-primary">
                            Approve <strong>{{ $adjustment->adjustment_number }}</strong>?
                        </p>
                        <p class="text-xs text-secondary mt-1">
                            This will immediately update stock levels for <strong>{{ $adjustment->items->count() }}</strong>
                            product(s) in <strong>{{ $adjustment->warehouse->name }}</strong>.
                            @php
                                $totalDiff = $adjustment->items->sum('difference');
                            @endphp
                            Net change: <strong class="{{ $totalDiff > 0 ? 'text-sage-600' : ($totalDiff < 0 ? 'text-red-600' : '') }}">
                                {{ $totalDiff > 0 ? '+' : '' }}{{ $totalDiff }}
                            </strong> units.
                        </p>
                        <p class="text-xs text-red-600/70 dark:text-red-300/70 mt-1">
                            <x-icon name="alert-triangle" class="w-3 h-3 inline" />
                            This action cannot be undone.
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-secondary mb-1.5">
                        Approval Notes (Optional)
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
                <button type="button" data-modal-close="approve-adjustment"
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
@endsection
