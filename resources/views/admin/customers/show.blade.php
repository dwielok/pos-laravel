@extends('layouts.admin')

@section('page-title', $customer->name)

@section('content')
    <div class="max-w-4xl space-y-5">
        <a href="{{ route('admin.customers.index') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Back to
            Customers</a>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">{{ $customer->name }}</h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        {{ $customer->phone ?? 'No phone' }} {{ $customer->email ? '· ' . $customer->email : '' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-500">Total Spent</p>
                    <p class="text-2xl font-semibold font-mono-num text-slate-900">{{ $totalSpent->formatted() }}</p>
                </div>
            </div>
            @if ($customer->address)
                <p class="mt-3 text-sm text-slate-600 bg-slate-50 rounded-lg p-3">{{ $customer->address }}</p>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-900">Purchase History</h3>
            </div>
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Invoice</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Warehouse</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Items</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Total</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($sales as $sale)
                        @php
                            $statusColors = [
                                'completed' => 'green',
                                'cancelled' => 'red',
                                'refunded' => 'amber',
                                'partially_refunded' => 'amber',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/75 cursor-pointer"
                            onclick="window.location='{{ route('admin.sales.show', $sale) }}'">
                            <td class="px-4 py-3 font-mono-num font-medium text-indigo-600">{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-3 text-slate-500">{{ $sale->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $sale->warehouse->name }}</td>
                            <td class="px-4 py-3 text-right font-mono-num">{{ $sale->items->sum('quantity') }}</td>
                            <td class="px-4 py-3 text-right font-mono-num font-medium">{{ $sale->total()->formatted() }}
                            </td>
                            <td class="px-4 py-3"><x-badge :color="$statusColors[$sale->status->value]">{{ $sale->status->label() }}</x-badge></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">No purchases yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($sales->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $sales->links() }}</div>
            @endif
        </div>
    </div>
@endsection
