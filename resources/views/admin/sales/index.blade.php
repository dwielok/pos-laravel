@extends('layouts.admin')

@section('page-title', 'Transactions')

@section('content')
    <div class="space-y-5">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Transactions</h2>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ $sales->total() }} transactions
                @unless (auth()->user()->can('sales.view-all'))
                    <span class="text-slate-400">(showing your transactions only)</span>
                @endunless
            </p>
        </div>

        <form method="GET"
            class="bg-white rounded-xl border border-slate-200 p-4 grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Invoice #..."
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <select name="status"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All statuses</option>
                <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Completed</option>
                <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Cancelled</option>
                <option value="refunded" @selected(($filters['status'] ?? '') === 'refunded')>Refunded</option>
                <option value="partially_refunded" @selected(($filters['status'] ?? '') === 'partially_refunded')>Partially Refunded</option>
            </select>
            <select name="warehouse_id"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All warehouses</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected(($filters['warehouse_id'] ?? null) == $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="submit"
                class="rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2">Filter</button>
        </form>

        @if (auth()->user()->can('pos-sync-audits.view'))
            <label
                class="flex items-center gap-2 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2.5 w-fit">
                <input type="checkbox" id="deviation-only-toggle" @checked($filters['deviation_only'] ?? false)
                    class="rounded border-amber-400 text-amber-600 focus:ring-amber-500">
                Show only sales with price deviations (synced offline at a different price than current catalog)
            </label>
        @endif

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
                            <td class="px-4 py-3 font-mono-num font-medium text-indigo-600">
                                <div class="flex items-center gap-1.5">
                                    {{ $sale->invoice_number }}
                                    @if ($sale->was_created_offline)
                                        <span title="Synced from an offline sale">
                                            <x-icon name="exclamation" class="w-3.5 h-3.5 text-amber-500" />
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $sale->created_at->format('M d, Y g:i A') }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $sale->customer->is_guest ? 'Walk-in' : $sale->customer->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $sale->cashier->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $sale->warehouse->name }}</td>
                            <td class="px-4 py-3 text-right font-mono-num font-medium">{{ $sale->total()->formatted() }}
                            </td>
                            <td class="px-4 py-3"><x-badge :color="$statusColors[$sale->status->value]">{{ $sale->status->label() }}</x-badge></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">No transactions found.</td>
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

@push('scripts')
    <script>
        $(function() {
            $('#deviation-only-toggle').on('change', function() {
                const url = new URL(window.location.href);
                if (this.checked) {
                    url.searchParams.set('deviation_only', '1');
                } else {
                    url.searchParams.delete('deviation_only');
                }
                window.location.href = url.toString();
            });
        });
    </script>
@endpush
