@extends('layouts.admin')

@section('page-title', 'Stock Movement History')

@section('content')
    <div class="space-y-5">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Stock Movement History</h2>
            <p class="text-sm text-slate-500 mt-0.5">Full audit ledger of every stock change across all warehouses.</p>
        </div>

        <form method="GET" class="bg-white rounded-xl border border-slate-200 p-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <select name="warehouse_id"
                class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All warehouses</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected(($filters['warehouse_id'] ?? null) == $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
            <select name="type" class="rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All movement types</option>
                @foreach (\App\Enums\StockMovementType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(($filters['type'] ?? '') === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
            <button type="submit"
                class="rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2">Filter</button>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Product</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Warehouse</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Type</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Change</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Balance</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($movements as $movement)
                        <tr class="hover:bg-slate-50/75">
                            <td class="px-4 py-3 text-slate-500 whitespace-nowrap">
                                {{ $movement->created_at->format('M d, Y g:i A') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-slate-900">{{ $movement->product->name }}</p>
                                <p class="text-xs text-slate-400 font-mono-num">{{ $movement->product->sku }}</p>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $movement->warehouse->name }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <x-badge :color="$movement->quantity >= 0 ? 'green' : 'red'">{{ $movement->type->label() }}</x-badge>
                                    @if ($movement->is_from_offline_sync)
                                        <span title="Synced from an offline POS sale">
                                            <x-icon name="exclamation" class="w-3.5 h-3.5 text-amber-500" />
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td
                                class="px-4 py-3 text-right font-mono-num font-medium @if ($movement->quantity >= 0) text-emerald-600 @else text-red-600 @endif">
                                {{ $movement->quantity >= 0 ? '+' : '' }}{{ $movement->quantity }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono-num text-slate-700">
                                {{ $movement->quantity_before }} &rarr; {{ $movement->quantity_after }}
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $movement->user->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">No stock movements recorded
                                yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($movements->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">{{ $movements->links() }}</div>
            @endif
        </div>
    </div>
@endsection
