@extends('layouts.admin')

@section('page-title', $sale->invoice_number)

@section('content')
    <div class="space-y-5">
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.sales.index') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Back to
                Transactions</a>
            <div class="flex gap-2">
                <a href="{{ route('admin.sales.reprint', $sale) }}" target="_blank"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Print
                    Receipt</a>
                <a href="{{ route('admin.sales.reprint-pdf', $sale) }}" target="_blank"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Download
                    PDF</a>
                @can('cancel', $sale)
                    <button type="button" data-modal-target="cancel-sale"
                        class="rounded-lg border border-red-300 text-red-600 hover:bg-red-50 text-sm font-medium px-4 py-2">Cancel
                        Sale</button>
                @endcan
                @can('refund', $sale)
                    <button type="button" data-modal-target="refund-sale"
                        class="rounded-lg bg-amber-600 hover:bg-amber-500 text-sm font-medium px-4 py-2 text-white">Process
                        Refund</button>
                @endcan
            </div>
        </div>

        @if ($sale->has_price_deviation)
            <div
                class="flex items-start gap-2 rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800">
                <x-icon name="exclamation" class="w-4 h-4 mt-0.5 shrink-0" />
                <span>This sale was synced from an offline register and one or more item prices differ from the current
                    catalog price. The customer was charged the price shown on their device at the time of sale.</span>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 font-mono-num">{{ $sale->invoice_number }}</h2>
                    <p class="text-sm text-slate-500 mt-0.5">{{ $sale->created_at->format('M d, Y \a\t g:i A') }}</p>
                    @if ($sale->was_created_offline)
                        <p class="text-xs text-amber-600 mt-1">
                            Recorded offline at {{ \Carbon\Carbon::parse($sale->created_offline_at)->format('g:i A') }},
                            synced at {{ $sale->synced_at?->format('g:i A') }}
                            via register {{ $sale->register->name ?? 'unknown' }}
                        </p>
                    @endif
                </div>
                @php
                    $statusColors = [
                        'completed' => 'green',
                        'cancelled' => 'red',
                        'refunded' => 'amber',
                        'partially_refunded' => 'amber',
                    ];
                @endphp
                <x-badge :color="$statusColors[$sale->status->value]" class="text-sm px-3 py-1">{{ $sale->status->label() }}</x-badge>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-slate-100 text-sm">
                <div>
                    <p class="text-slate-500">Customer</p>
                    <p class="font-medium text-slate-900 mt-0.5">
                        {{ $sale->customer->is_guest ? 'Walk-in Customer' : $sale->customer->name }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Cashier</p>
                    <p class="font-medium text-slate-900 mt-0.5">{{ $sale->cashier->name }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Warehouse</p>
                    <p class="font-medium text-slate-900 mt-0.5">{{ $sale->warehouse->name }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Payment</p>
                    <p class="font-medium text-slate-900 mt-0.5">
                        {{ $sale->payments->pluck('method')->map(fn($m) => $m->label())->join(', ') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Product</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Qty</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Unit Price</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Refunded</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($sale->items as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900">{{ $item->product_name_snapshot }}</p>
                                <p class="text-xs text-slate-400 font-mono-num">{{ $item->product_sku_snapshot }}</p>
                                @if ($item->hasPriceDeviation())
                                    <p class="text-xs text-amber-600 mt-0.5">
                                        Catalog price now
                                        {{ \App\Support\Money::fromUnits($item->current_price_at_sync_cents)->formatted() }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono-num">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right font-mono-num">{{ $item->unitPrice()->formatted() }}</td>
                            <td class="px-4 py-3 text-right font-mono-num text-slate-500">
                                {{ $item->refunded_quantity > 0 ? $item->refunded_quantity : '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono-num font-medium">
                                {{ \App\Support\Money::fromUnits($item->total_cents)->formatted() }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50">
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right text-slate-500">Subtotal</td>
                        <td class="px-4 py-2 text-right font-mono-num">
                            {{ \App\Support\Money::fromUnits($sale->subtotal_cents)->formatted() }}</td>
                    </tr>
                    @if ($sale->discount_cents > 0)
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-right text-slate-500">Discount</td>
                            <td class="px-4 py-2 text-right font-mono-num">
                                -{{ \App\Support\Money::fromUnits($sale->discount_cents)->formatted() }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right text-slate-500">Tax</td>
                        <td class="px-4 py-2 text-right font-mono-num">
                            {{ \App\Support\Money::fromUnits($sale->tax_cents)->formatted() }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-slate-900">Total</td>
                        <td class="px-4 py-3 text-right font-mono-num font-semibold text-slate-900">
                            {{ $sale->total()->formatted() }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right text-slate-500">Paid</td>
                        <td class="px-4 py-2 text-right font-mono-num">
                            {{ \App\Support\Money::fromUnits($sale->paid_cents)->formatted() }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right text-slate-500">Change</td>
                        <td class="px-4 py-2 text-right font-mono-num">
                            {{ \App\Support\Money::fromUnits($sale->change_cents)->formatted() }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if ($sale->refunds->isNotEmpty())
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-900">Refund History</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($sale->refunds as $refund)
                        <div class="px-5 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-slate-900">
                                        {{ \App\Support\Money::fromUnits($refund->amount_cents)->formatted() }} refunded
                                    </p>
                                    <p class="text-sm text-slate-500">{{ $refund->reason }} ·
                                        {{ $refund->processedBy->name }} ·
                                        {{ $refund->created_at->format('M d, Y g:i A') }}</p>
                                </div>
                                <x-badge
                                    color="amber">{{ ucwords(str_replace('_', ' ', $refund->refund_method)) }}</x-badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Cancel modal --}}
    <x-modal id="cancel-sale" title="Cancel Sale">
        <form method="POST" action="{{ route('admin.sales.cancel', $sale) }}">
            @csrf
            <p class="text-sm text-slate-600 mb-3">This will void the entire sale and restore all item quantities to stock.
            </p>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Reason <span
                        class="text-red-500">*</span></label>
                <textarea name="reason" rows="2" required
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-modal-close="cancel-sale"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Keep
                    Sale</button>
                <button type="submit"
                    class="rounded-lg bg-red-600 hover:bg-red-500 text-sm font-medium px-4 py-2 text-white">Cancel
                    Sale</button>
            </div>
        </form>
    </x-modal>

    {{-- Refund modal --}}
    <x-modal id="refund-sale" title="Process Refund" maxWidth="lg">
        <form method="POST" action="{{ route('admin.sales.refund', $sale) }}">
            @csrf
            <p class="text-sm text-slate-500 mb-3">Enter the quantity to refund for each item.</p>
            <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                @foreach ($sale->items as $item)
                    @php $refundable = $item->quantityRefundable(); @endphp
                    <div
                        class="flex items-center justify-between gap-3 @if ($refundable === 0) opacity-50 @endif">
                        <div class="min-w-0">
                            <p class="font-medium text-slate-900 text-sm truncate">{{ $item->product_name_snapshot }}</p>
                            <p class="text-xs text-slate-500">Refundable: {{ $refundable }} of {{ $item->quantity }}
                            </p>
                        </div>
                        <input type="number" min="0" max="{{ $refundable }}" value="0"
                            name="quantities[{{ $item->id }}]" {{ $refundable === 0 ? 'disabled' : '' }}
                            class="w-20 rounded-lg border-slate-300 text-sm font-mono-num text-right">
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Refund Method</label>
                    <select name="refund_method"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="store_credit">Store Credit</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Reason <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="reason" required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button" data-modal-close="refund-sale"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit"
                    class="rounded-lg bg-amber-600 hover:bg-amber-500 text-sm font-medium px-4 py-2 text-white">Process
                    Refund</button>
            </div>
        </form>
    </x-modal>
@endsection
