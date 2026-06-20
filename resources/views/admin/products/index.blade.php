@extends('layouts.admin')

@section('page-title', 'Products')

@section('content')
    <div class="space-y-5">

        {{-- Header actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Products</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ $products->total() }} total products in catalog</p>
            </div>
            @can('create', \App\Models\Product::class)
                <a href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition">
                    <x-icon name="plus" class="w-4 h-4" />
                    Add Product
                </a>
            @endcan
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.products.index') }}"
            class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
                    <div class="relative">
                        <x-icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                            placeholder="Name, SKU, or barcode..."
                            class="w-full rounded-lg border-slate-300 pl-9 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Category</label>
                    <select name="category_id"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? null) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                    <select name="status"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All statuses</option>
                        <option value="active" @selected(($filters['status'] ?? null) === 'active')>Active</option>
                        <option value="inactive" @selected(($filters['status'] ?? null) === 'inactive')>Inactive</option>
                        <option value="discontinued" @selected(($filters['status'] ?? null) === 'discontinued')>Discontinued</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <label class="flex items-center gap-2 text-sm text-slate-700 mb-2">
                        <input type="checkbox" name="low_stock_only" value="1" @checked($filters['low_stock_only'] ?? false)
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        Low stock only
                    </label>
                </div>
            </div>
            <div class="mt-3 flex gap-2">
                <button type="submit"
                    class="rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-4 py-2 transition">
                    Apply Filters
                </button>
                <a href="{{ route('admin.products.index') }}"
                    class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50 transition">
                    Reset
                </a>
            </div>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-slate-500">Product</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-500">SKU / Barcode</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-500">Category</th>
                            <th class="px-4 py-3 text-right font-medium text-slate-500">Cost</th>
                            <th class="px-4 py-3 text-right font-medium text-slate-500">Price</th>
                            <th class="px-4 py-3 text-right font-medium text-slate-500">Stock</th>
                            <th class="px-4 py-3 text-left font-medium text-slate-500">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($products as $product)
                            @php $stock = $product->totalStock(); @endphp
                            <tr class="hover:bg-slate-50/75 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}"
                                                class="w-9 h-9 rounded-lg object-cover border border-slate-200"
                                                alt="">
                                        @else
                                            <div
                                                class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                                                <x-icon name="photo" class="w-4 h-4" />
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-medium text-slate-900 truncate">{{ $product->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $product->unit->symbol ?? '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-mono-num text-slate-600">
                                    <div>{{ $product->sku }}</div>
                                    @if ($product->barcode)
                                        <div class="text-xs text-slate-400">{{ $product->barcode }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $product->category->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right font-mono-num text-slate-600">
                                    {{ $product->costPrice()->formatted() }}</td>
                                <td class="px-4 py-3 text-right font-mono-num font-medium text-slate-900">
                                    {{ $product->sellingPrice()->formatted() }}</td>
                                <td class="px-4 py-3 text-right font-mono-num">
                                    @if ($product->isLowStock())
                                        <x-badge color="amber">{{ $stock }} low</x-badge>
                                    @else
                                        <span class="text-slate-700">{{ $stock }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($product->status === 'active')
                                        <x-badge color="green">Active</x-badge>
                                    @elseif ($product->status === 'inactive')
                                        <x-badge color="slate">Inactive</x-badge>
                                    @else
                                        <x-badge color="red">Discontinued</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        @can('update', $product)
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                                class="p-1.5 rounded-md text-slate-500 hover:bg-slate-100 hover:text-indigo-600 transition"
                                                title="Edit">
                                                <x-icon name="pencil" class="w-4 h-4" />
                                            </a>
                                        @endcan
                                        @can('delete', $product)
                                            <button type="button" data-modal-target="delete-product-{{ $product->id }}"
                                                class="p-1.5 rounded-md text-slate-500 hover:bg-red-50 hover:text-red-600 transition"
                                                title="Delete">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>

                                            <x-modal id="delete-product-{{ $product->id }}" title="Delete Product">
                                                <p class="text-sm text-slate-600">
                                                    Are you sure you want to delete <strong>{{ $product->name }}</strong>?
                                                    This will hide it from the catalog but preserve historical sales records.
                                                </p>
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                                    class="mt-4 flex justify-end gap-2">
                                                    @csrf @method('DELETE')
                                                    <button type="button"
                                                        data-modal-close="delete-product-{{ $product->id }}"
                                                        class="rounded-lg border border-slate-300 text-sm font-medium px-4 py-2 text-slate-600 hover:bg-slate-50">Cancel</button>
                                                    <button type="submit"
                                                        class="rounded-lg bg-red-600 hover:bg-red-500 text-sm font-medium px-4 py-2 text-white">Delete</button>
                                                </form>
                                            </x-modal>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-slate-500">
                                    <p class="font-medium">No products found</p>
                                    <p class="text-sm mt-1">Try adjusting your filters, or add your first product.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
