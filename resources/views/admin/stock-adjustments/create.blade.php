@extends('layouts.admin')

@section('page-title', 'New Stock Adjustment')
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
                    <h2 class="text-xl font-semibold text-primary">New Stock Adjustment</h2>
                    <div class="flex items-center gap-2 text-sm text-secondary">
                        <a href="{{ route('admin.stock-adjustments.index') }}"
                            class="hover:text-sage-600 dark:hover:text-sage-400 transition flex items-center gap-1">
                            <x-icon name="chevron-left" class="w-3 h-3" />
                            Back to Adjustments
                        </a>
                        <span class="w-1 h-1 rounded-full bg-sage-300 dark:bg-sage-600 opacity-30"></span>
                        <span>Manual stock correction</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs text-sage-600 dark:text-sage-400 bg-sage-100/50 dark:bg-sage-800/30 px-3 py-1.5 rounded-full border border-sage-200 dark:border-sage-700">
                <span class="w-1.5 h-1.5 rounded-full bg-sage-500 dark:bg-sage-400 animate-pulse"></span>
                Draft will be saved for approval
            </div>
        </div>

        <form method="POST" action="{{ route('admin.stock-adjustments.store') }}" id="adjustment-form">
            @csrf

            {{-- Adjustment Details --}}
            <div class="bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                        <x-icon name="clipboard-list" class="w-4 h-4" />
                    </div>
                    <h3 class="font-semibold text-primary">Adjustment Details</h3>
                    <span class="ml-auto text-xs text-secondary bg-sage-100/50 dark:bg-sage-800/30 px-2.5 py-1 rounded-full border border-sage-200 dark:border-sage-700">Required fields *</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">
                            Warehouse <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                <x-icon name="warehouse" class="w-4 h-4" />
                            </div>
                            <select name="warehouse_id" id="warehouse-select" required
                                class="w-full rounded-xl border-theme pl-9 pr-10 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition appearance-none cursor-pointer">
                                <option value="">Select warehouse...</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            {{-- <div class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary opacity-40 pointer-events-none">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div> --}}
                        </div>
                        @error('warehouse_id')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-secondary mb-1.5">
                            Reason <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-40">
                                <x-icon name="info" class="w-4 h-4" />
                            </div>
                            <select name="reason" required
                                class="w-full rounded-xl border-theme pl-9 pr-10 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition appearance-none cursor-pointer">
                                <option value="stock_count">Stock Count / Recount</option>
                                <option value="damaged">Damaged Goods</option>
                                <option value="expired">Expired Goods</option>
                                <option value="theft_loss">Theft / Loss</option>
                                <option value="found">Found Stock</option>
                                <option value="other">Other</option>
                            </select>
                            {{-- <div class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary opacity-40 pointer-events-none">
                                <x-icon name="chevron-down" class="w-4 h-4" />
                            </div> --}}
                        </div>
                        @error('reason')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-secondary mb-1.5">Notes</label>
                    <div class="relative">
                        <div class="absolute left-3 top-3 text-secondary opacity-40">
                            <x-icon name="info" class="w-4 h-4" />
                        </div>
                        <textarea name="notes" rows="2" placeholder="Explain the reason for this adjustment..."
                            class="w-full rounded-xl border-theme pl-9 pr-4 py-2.5 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Items Section --}}
            <div class="bg-card rounded-2xl border border-theme p-6 shadow-sm hover:shadow-md transition-shadow mt-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-sage-100 dark:bg-sage-800/30 text-sage-600 dark:text-sage-400 flex items-center justify-center">
                            <x-icon name="cube" class="w-4 h-4" />
                        </div>
                        <h3 class="font-semibold text-primary">Items to Adjust</h3>
                        <span class="text-xs text-secondary bg-sage-100/50 dark:bg-sage-800/30 px-2.5 py-1 rounded-full border border-sage-200 dark:border-sage-700">
                            <span id="item-count">0</span> items
                        </span>
                    </div>
                    <button type="button" id="add-item-btn"
                        class="inline-flex items-center gap-2 rounded-xl border border-theme hover:bg-sage-100 dark:hover:bg-sage-800/30 hover:text-sage-700 dark:hover:text-sage-300 px-4 py-2 text-sm font-medium text-secondary transition group">
                        <x-icon name="plus" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" />
                        Add Product
                    </button>
                </div>
                <p class="text-xs text-secondary mb-3">System quantity is fetched automatically once you select a warehouse and product.</p>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm" id="items-table">
                        <thead>
                            <tr class="text-left text-xs font-medium text-secondary uppercase tracking-wider border-b border-theme">
                                <th class="pb-3 pr-2">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="tag" class="w-3.5 h-3.5" />
                                        Product
                                    </span>
                                </th>
                                <th class="pb-3 px-2 w-28 text-right">
                                    <span class="flex items-center justify-end gap-1.5">
                                        <x-icon name="inbox" class="w-3.5 h-3.5" />
                                        System Qty
                                    </span>
                                </th>
                                <th class="pb-3 px-2 w-28">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="clipboard-list" class="w-3.5 h-3.5" />
                                        Counted Qty
                                    </span>
                                </th>
                                <th class="pb-3 px-2 w-24 text-right">
                                    <span class="flex items-center justify-end gap-1.5">
                                        <x-icon name="adjustments" class="w-3.5 h-3.5" />
                                        Difference
                                    </span>
                                </th>
                                <th class="pb-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="items-tbody" class="divide-y divide-theme"></tbody>
                    </table>
                </div>
                @error('items')
                    <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                        <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 bg-card rounded-2xl border border-theme p-4 shadow-sm">
                <div class="text-sm text-secondary">
                    <span class="font-medium text-primary">*</span> Required fields
                    <span class="inline-block w-1 h-1 rounded-full bg-sage-300 dark:bg-sage-600 opacity-30 mx-2"></span>
                    Stock levels are only changed after approval
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.stock-adjustments.index') }}"
                        class="flex-1 sm:flex-none rounded-xl border border-theme text-sm font-medium px-6 py-2.5 text-secondary hover:bg-sage-50 dark:hover:bg-sage-900/20 hover:text-primary transition text-center">
                        Cancel
                    </a>
                    <button type="submit"
                        class="flex-1 sm:flex-none rounded-xl bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-sm font-medium px-6 py-2.5 text-white shadow-sm hover:shadow-md transition flex items-center justify-center gap-2 group">
                        <x-icon name="save" class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                        Save as Draft
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Row template --}}
    <template id="adj-item-row-template">
        <tr class="item-row">
            <td class="py-2 pr-2">
                <div class="relative">
                    <select name="items[__INDEX__][product_id]"
                        class="product-select w-full rounded-xl border-theme pl-3 pr-8 py-2 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition appearance-none cursor-pointer"
                        required>
                        <option value="">Select product...</option>
                    </select>
                    {{-- <div class="absolute right-2 top-1/2 -translate-y-1/2 text-secondary opacity-40 pointer-events-none">
                        <x-icon name="chevron-down" class="w-4 h-4" />
                    </div> --}}
                </div>
            </td>
            <td class="py-2 px-2 text-right font-mono-num system-qty-display text-secondary">—</td>
            <td class="py-2 px-2">
                <input type="number" name="items[__INDEX__][counted_quantity]" min="0" value="0"
                    class="counted-input w-full rounded-xl border-theme px-3 py-2 bg-sage-50/50 dark:bg-sage-900/20 text-primary text-sm font-mono-num text-right focus:ring-2 focus:ring-sage-400 dark:focus:ring-sage-500 focus:border-sage-400 dark:focus:border-sage-500 transition"
                    required>
                <input type="hidden" name="items[__INDEX__][system_quantity]" class="system-qty-input" value="0">
            </td>
            <td class="py-2 px-2 text-right font-mono-num diff-display text-secondary">0</td>
            <td class="py-2 text-right">
                <button type="button"
                    class="remove-item-btn p-1.5 rounded-lg text-secondary hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition">
                    <x-icon name="trash" class="w-4 h-4" />
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script>
        let itemIndex = 0;

        $(function() {
            function addItemRow() {
                const template = document.getElementById('adj-item-row-template').innerHTML.replaceAll('__INDEX__',
                    itemIndex);
                const $row = $(template);
                $row.find('.product-select').append($('#product-options-source').html());
                $('#items-tbody').append($row);
                itemIndex++;
                updateItemCount();
            }

            function updateItemCount() {
                const count = $('.item-row').length;
                $('#item-count').text(count);
            }

            function recalcDiff($row) {
                const system = parseInt($row.find('.system-qty-input').val()) || 0;
                const counted = parseInt($row.find('.counted-input').val()) || 0;
                const diff = counted - system;
                const $diffCell = $row.find('.diff-display');
                $diffCell.text((diff > 0 ? '+' : '') + diff);
                if (diff > 0) {
                    $diffCell.removeClass('text-secondary text-red-600').addClass('text-sage-600 dark:text-sage-400');
                } else if (diff < 0) {
                    $diffCell.removeClass('text-secondary text-sage-600').addClass('text-red-600');
                } else {
                    $diffCell.removeClass('text-sage-600 text-red-600').addClass('text-secondary');
                }
            }

            // Fetches current system quantity for the selected product+warehouse
            $(document).on('change', '.product-select', function() {
                const $row = $(this).closest('tr');
                const warehouseId = $('#warehouse-select').val();
                const selected = $(this).find('option:selected');
                const stockJson = selected.attr('data-stock') || '{}';
                let stockMap = {};
                try {
                    stockMap = JSON.parse(stockJson);
                } catch (e) {}

                const systemQty = warehouseId && stockMap[warehouseId] !== undefined ? stockMap[
                    warehouseId] : 0;
                $row.find('.system-qty-display').text(systemQty);
                $row.find('.system-qty-input').val(systemQty);
                $row.find('.counted-input').val(systemQty);
                recalcDiff($row);
            });

            $(document).on('input', '.counted-input', function() {
                recalcDiff($(this).closest('tr'));
            });

            $('#warehouse-select').on('change', function() {
                $('.product-select').each(function() {
                    $(this).trigger('change');
                });
            });

            $('#add-item-btn').on('click', addItemRow);
            $(document).on('click', '.remove-item-btn', function() {
                $(this).closest('tr').remove();
                updateItemCount();
                if ($('.item-row').length === 0) addItemRow();
            });

            addItemRow();

            // Validate at least one item has a product selected
            $('#adjustment-form').on('submit', function(e) {
                let hasProduct = false;
                $('.product-select').each(function() {
                    if ($(this).val()) {
                        hasProduct = true;
                        return false;
                    }
                });
                if (!hasProduct) {
                    e.preventDefault();
                    alert('Please add at least one item to the adjustment.');
                }
            });
        });
    </script>

    {{-- Product options with per-warehouse stock embedded --}}
    <div id="product-options-source" class="hidden">
        @foreach (\App\Models\Product::active()->with('stockLevels')->orderBy('name')->get() as $product)
            @php
                $stockByWarehouse = $product->stockLevels->pluck('quantity', 'warehouse_id');
            @endphp
            <option value="{{ $product->id }}" data-stock='{{ $stockByWarehouse->toJson() }}'>{{ $product->name }}
                ({{ $product->sku }})
            </option>
        @endforeach
    </div>
@endpush
