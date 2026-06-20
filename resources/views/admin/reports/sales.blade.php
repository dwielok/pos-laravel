@extends('layouts.admin')

@section('page-title', 'Sales Report')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-900">Sales Report</h2>
            @can('reports.export')
                <div class="flex gap-2">
                    <a href="{{ route('admin.reports.sales.export', ['format' => 'csv'] + request()->query()) }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-3 py-2 text-slate-600 hover:bg-slate-50">CSV</a>
                    <a href="{{ route('admin.reports.sales.export', ['format' => 'xlsx'] + request()->query()) }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-3 py-2 text-slate-600 hover:bg-slate-50">Excel</a>
                    <a href="{{ route('admin.reports.sales.export', ['format' => 'pdf'] + request()->query()) }}"
                        class="rounded-lg border border-slate-300 text-sm font-medium px-3 py-2 text-slate-600 hover:bg-slate-50">PDF</a>
                </div>
            @endcan
        </div>

        @include('admin.reports._filters', ['route' => 'admin.reports.sales'])

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <x-stat-card label="Transactions" :value="$report['totals']['transaction_count']" />
            <x-stat-card label="Subtotal" :value="number_format($report['totals']['subtotal'], 2)" />
            <x-stat-card label="Discounts" :value="number_format($report['totals']['discount'], 2)" />
            <x-stat-card label="Tax Collected" :value="number_format($report['totals']['tax'], 2)" />
            <x-stat-card label="Total Revenue" :value="number_format($report['totals']['total'], 2)" />
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Invoice</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Customer</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Cashier</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Warehouse</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($report['rows'] as $row)
                        <tr>
                            <td class="px-4 py-2.5 font-mono-num text-indigo-600">{{ $row['invoice_number'] }}</td>
                            <td class="px-4 py-2.5 text-slate-500">{{ $row['date'] }}</td>
                            <td class="px-4 py-2.5 text-slate-600">{{ $row['customer'] }}</td>
                            <td class="px-4 py-2.5 text-slate-600">{{ $row['cashier'] }}</td>
                            <td class="px-4 py-2.5 text-slate-600">{{ $row['warehouse'] }}</td>
                            <td class="px-4 py-2.5 text-right font-mono-num font-medium">
                                {{ number_format($row['total'], 2) }}</td>
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
