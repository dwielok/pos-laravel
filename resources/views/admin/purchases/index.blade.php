@extends('layouts.admin')

@section('page-title', 'Purchases')

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Purchase Orders</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ $purchases->total() }} purchase orders</p>
            </div>
            @can('create', \App\Models\Purchase::class)
                <a href="{{ route('admin.purchases.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm">
                    <x-icon name="plus" class="w-4 h-4" /> New Purchase
                </a>
            @endcan
        </div>

        <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 grid grid-cols-1 sm:grid-cols-4 gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="PO number..."
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <select name="status"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All statuses</option>
                @foreach (['draft', 'ordered', 'partially_received', 'received', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                        {{ ucwords(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <select name="supplier_id"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All suppliers</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(($filters['supplier_id'] ?? null) == $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2">Filter</button>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">PO Number</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Supplier</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Warehouse</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Order Date</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Total</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($purchases as $purchase)
                        <tr class="hover:bg-slate-50/75 cursor-pointer"
                            onclick="window.location='{{ route('admin.purchases.show', $purchase) }}'">
                            <td class="px-4 py-3 font-mono-num font-medium text-indigo-600">{{ $purchase->purchase_number }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $purchase->supplier->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $purchase->warehouse->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $purchase->order_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right font-mono-num font-medium text-slate-900">
                                {{ $purchase->total()->formatted() }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'draft' => 'slate',
                                        'ordered' => 'indigo',
                                        'partially_received' => 'amber',
                                        'received' => 'green',
                                        'cancelled' => 'red',
                                    ];
                                @endphp
                                <x-badge :color="$statusColors[$purchase->status->value]">{{ $purchase->status->label() }}</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">No purchase orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($purchases->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $purchases->links() }}</div>
            @endif
        </div>
    </div>
@endsection
