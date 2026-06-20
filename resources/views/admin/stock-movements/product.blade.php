@extends('layouts.admin')

@section('page-title', 'Stock History: ' . $product->name)

@section('content')
    <div class="space-y-5">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Back to
            Products</a>

        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            @if ($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}"
                    class="w-14 h-14 rounded-lg object-cover border border-slate-200">
            @else
                <div class="w-14 h-14 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                    <x-icon name="photo" class="w-6 h-6" />
                </div>
            @endif
            <div>
                <h2 class="text-lg font-semibold text-slate-900">{{ $product->name }}</h2>
                <p class="text-sm text-slate-500 font-mono-num">{{ $product->sku }}</p>
            </div>
            <div class="ml-auto text-right">
                <p class="text-sm text-slate-500">Total Stock (all warehouses)</p>
                <p
                    class="text-2xl font-semibold font-mono-num {{ $product->isLowStock() ? 'text-amber-600' : 'text-slate-900' }}">
                    {{ $product->totalStock() }}
                </p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Warehouse</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Type</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Change</th>
                        <th class="px-4 py-3 text-right font-medium text-slate-500">Balance</th>
                        <th class="px-4 py-3 text-left font-medium text-slate-500">Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($movements as $movement)
                        <tr class="hover:bg-slate-50/75">
                            <td class="px-4 py-3 text-slate-500 whitespace-nowrap">
                                {{ $movement->created_at->format('M d, Y g:i A') }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $movement->warehouse->name }}</td>
                            <td class="px-4 py-3"><x-badge :color="$movement->quantity >= 0 ? 'green' : 'red'">{{ $movement->type->label() }}</x-badge></td>
                            <td
                                class="px-4 py-3 text-right font-mono-num font-medium @if ($movement->quantity >= 0) text-emerald-600 @else text-red-600 @endif">
                                {{ $movement->quantity >= 0 ? '+' : '' }}{{ $movement->quantity }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono-num text-slate-700">{{ $movement->quantity_after }}
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $movement->note ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">No movements recorded for this
                                product yet.</td>
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
