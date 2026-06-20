@extends('layouts.admin')

@section('page-title', 'Profit Report')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Profit Report</h2>
            @can('reports.export')
                <div class="flex gap-2">
                    <a href="{{ route('admin.reports.profit.export', ['format' => 'csv'] + request()->query()) }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-3 py-2 text-slate-600 hover:bg-slate-50">CSV</a>
                    <a href="{{ route('admin.reports.profit.export', ['format' => 'xlsx'] + request()->query()) }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-3 py-2 text-slate-600 hover:bg-slate-50">Excel</a>
                    <a href="{{ route('admin.reports.profit.export', ['format' => 'pdf'] + request()->query()) }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-3 py-2 text-slate-600 hover:bg-slate-50">PDF</a>
                </div>
            @endcan
        </div>

        @include('admin.reports._filters', ['route' => 'admin.reports.profit'])

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <x-stat-card label="Revenue" :value="number_format($report['totals']['revenue'], 2)" />
            <x-stat-card label="Cost of Goods Sold" :value="number_format($report['totals']['cost'], 2)" />
            <x-stat-card label="Gross Profit" :value="number_format($report['totals']['profit'], 2)" />
            <x-stat-card label="Margin" :value="$report['totals']['margin_percent'] . '%'" />
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Product</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Qty Sold</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Revenue</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Cost (COGS)</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Profit</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($report['rows'] as $row)
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-slate-900">{{ $row['product_name'] }}</td>
                            <td class="px-4 py-2.5 text-right font-mono-num">{{ $row['quantity_sold'] }}</td>
                            <td class="px-4 py-2.5 text-right font-mono-num">{{ number_format($row['revenue'], 0) }}</td>
                            <td class="px-4 py-2.5 text-right font-mono-num text-slate-500">
                                {{ number_format($row['cost'], 0) }}</td>
                            <td
                                class="px-4 py-2.5 text-right font-mono-num font-medium @if ($row['profit'] < 0) text-red-600 @else text-emerald-600 @endif">
                                {{ number_format($row['profit'], 0) }}
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono-num text-slate-600">{{ $row['margin_percent'] }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">No sales in this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
