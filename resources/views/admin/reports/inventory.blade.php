@extends('layouts.admin')

@section('page-title', 'Inventory Report')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Inventory Report</h2>
            @can('reports.export')
                <div class="flex gap-2">
                    <a href="{{ route('admin.reports.inventory.export', ['format' => 'csv'] + request()->query()) }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-3 py-2 text-slate-600 hover:bg-slate-50">CSV</a>
                    <a href="{{ route('admin.reports.inventory.export', ['format' => 'xlsx'] + request()->query()) }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-3 py-2 text-slate-600 hover:bg-slate-50">Excel</a>
                    <a href="{{ route('admin.reports.inventory.export', ['format' => 'pdf'] + request()->query()) }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-3 py-2 text-slate-600 hover:bg-slate-50">PDF</a>
                </div>
            @endcan
        </div>

        @include('admin.reports._filters', ['route' => 'admin.reports.inventory'])

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <x-stat-card label="Products Tracked" :value="$report['totals']['product_count']" />
            <x-stat-card label="Low Stock Items" :value="$report['totals']['low_stock_count']" />
            <x-stat-card label="Stock Value (at cost)" :value="number_format($report['totals']['total_value_at_cost'], 2)" />
            <x-stat-card label="Stock Value (at price)" :value="number_format($report['totals']['total_value_at_price'], 2)" />
        </div>

        @if ($report['movement_summary']->isNotEmpty())
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <h3 class="font-semibold text-slate-900 mb-3">Movement Activity in Period</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                    @foreach ($report['movement_summary'] as $type => $total)
                        <div class="bg-slate-50 rounded-lg px-3 py-2">
                            <p class="text-slate-500 text-xs">{{ ucwords(str_replace('_', ' ', $type)) }}</p>
                            <p class="font-mono-num font-semibold text-slate-900">{{ $total }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Product</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">SKU</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Quantity</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Value (cost)</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Value (price)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($report['stock_rows'] as $row)
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-slate-900">{{ $row['name'] }}</td>
                            <td class="px-4 py-2.5 font-mono-num text-slate-500">{{ $row['sku'] }}</td>
                            <td class="px-4 py-2.5 text-right font-mono-num">
                                @if ($row['is_low_stock'])
                                    <x-badge color="amber">{{ $row['quantity'] }}</x-badge>
                                @else
                                    {{ $row['quantity'] }}
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono-num text-slate-600">
                                {{ number_format($row['stock_value_at_cost'], 2) }}</td>
                            <td class="px-4 py-2.5 text-right font-mono-num text-slate-600">
                                {{ number_format($row['stock_value_at_price'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">No tracked products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
