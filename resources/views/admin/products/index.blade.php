@extends('layouts.admin')

@section('page-title', 'Products')
@section('breadcrumb', 'Inventory Management')

@section('content')
    <div class="space-y-6">

        {{-- Header actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-primary-green-light text-primary-green flex items-center justify-center">
                        <x-icon name="cube" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-primary">Products</h2>
                        <div class="flex items-center gap-3 text-sm text-secondary">
                            <span>{{ $products->total() }} total products</span>
                            <span class="w-1 h-1 rounded-full bg-secondary opacity-30"></span>
                            <span class="flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                {{ $products->where('status', 'active')->count() }} active
                            </span>
                            @if ($lowStockCount = $products->filter(fn($p) => $p->isLowStock())->count())
                                <span class="w-1 h-1 rounded-full bg-secondary opacity-30"></span>
                                <span class="flex items-center gap-1 text-amber-600">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    {{ $lowStockCount }} low stock
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @can('create', \App\Models\Product::class)
                <a href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-green hover:bg-primary-green-dark px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:shadow-md transition-all duration-200 group">
                    <x-icon name="plus" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-200" />
                    Add Product
                </a>
            @endcan
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Value</p>
                <p class="text-lg font-bold text-primary mt-1 font-mono-num">
                    {{ \App\Support\Money::fromAmount($products->sum(fn($p) => $p->sellingPrice()->amount() * $p->totalStock()))->formatted() }}
                </p>
            </div>
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Categories</p>
                <p class="text-lg font-bold text-primary mt-1">{{ $categories->count() }}</p>
            </div>
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Avg. Price</p>
                <p class="text-lg font-bold text-primary mt-1 font-mono-num">
                    {{ $products->count() ? \App\Support\Money::fromAmount($products->avg(fn($p) => $p->sellingPrice()->amount()))->formatted() : '—' }}
                </p>
            </div>
            <div class="bg-card rounded-xl border border-theme p-4 shadow-sm">
                <p class="text-xs font-medium text-secondary uppercase tracking-wider">Total Stock</p>
                <p class="text-lg font-bold text-primary mt-1 font-mono-num">
                    {{ $products->sum(fn($p) => $p->totalStock()) }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.products.index') }}"
            class="bg-card rounded-2xl border border-theme p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-medium text-secondary uppercase tracking-wider mb-1.5">Search</label>
                    <div class="relative">
                        <x-icon name="search"
                            class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-secondary opacity-50" />
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                            placeholder="Search by name, SKU, or barcode..."
                            class="w-full rounded-xl border-theme pl-9 pr-4 py-2 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-secondary uppercase tracking-wider mb-1.5">Category</label>
                    <select name="category_id"
                        class="w-full rounded-xl border-theme px-4 py-2 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition appearance-none cursor-pointer">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? null) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-secondary uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status"
                        class="w-full rounded-xl border-theme px-4 py-2 bg-primary-green-light/10 text-sm focus:ring-2 focus:ring-primary-green focus:border-transparent transition appearance-none cursor-pointer">
                        <option value="">All statuses</option>
                        <option value="active" @selected(($filters['status'] ?? null) === 'active')>Active</option>
                        <option value="inactive" @selected(($filters['status'] ?? null) === 'inactive')>Inactive</option>
                        <option value="discontinued" @selected(($filters['status'] ?? null) === 'discontinued')>Discontinued</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2.5 text-sm text-secondary cursor-pointer group">
                        <input type="checkbox" name="low_stock_only" value="1" @checked($filters['low_stock_only'] ?? false)
                            class="w-4 h-4 rounded border-theme text-primary-green focus:ring-primary-green focus:ring-2 transition">
                        <span class="group-hover:text-primary transition">Show low stock only</span>
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    </label>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary-green hover:bg-primary-green-dark text-white text-sm font-medium px-5 py-2 transition shadow-sm hover:shadow-md">
                    <x-icon name="filter" class="w-4 h-4" />
                    Apply Filters
                </button>
                <a href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-theme text-sm font-medium px-5 py-2 text-secondary hover:bg-primary-green-light hover:text-primary transition">
                    <x-icon name="refresh" class="w-4 h-4" />
                    Reset
                </a>
                @if (request()->hasAny(['search', 'category_id', 'status', 'low_stock_only']))
                    <span class="text-xs text-secondary flex items-center px-3 py-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-green mr-1.5"></span>
                        {{ $products->total() }} results found
                    </span>
                @endif
            </div>
        </form>

        {{-- Table --}}
        <div class="bg-card rounded-2xl border border-theme overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-theme text-sm">
                    <thead class="bg-primary-green-light/20">
                        <tr>
                            <th class="px-4 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="cube" class="w-3.5 h-3.5" />
                                    Product
                                </span>
                            </th>
                            <th class="px-4 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="barcode" class="w-3.5 h-3.5" />
                                    SKU / Barcode
                                </span>
                            </th>
                            <th class="px-4 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="tag" class="w-3.5 h-3.5" />
                                    Category
                                </span>
                            </th>
                            <th class="px-4 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                Cost</th>
                            <th class="px-4 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                Price</th>
                            <th class="px-4 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="inbox" class="w-3.5 h-3.5" />
                                    Stock
                                </span>
                            </th>
                            <th class="px-4 py-3.5 text-left font-medium text-xs uppercase tracking-wider text-secondary">
                                Status</th>
                            <th class="px-4 py-3.5 text-right font-medium text-xs uppercase tracking-wider text-secondary">
                                <span class="flex items-center justify-end gap-1.5">
                                    <x-icon name="settings" class="w-3.5 h-3.5" />
                                    Actions
                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-theme">
                        @forelse ($products as $product)
                            @php $stock = $product->totalStock(); @endphp
                            <tr class="hover:bg-primary-green-light/10 transition group">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-primary-green-light/30 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                            @if ($product->image_path)
                                                <img src="{{ asset('storage/' . $product->image_path) }}"
                                                    class="w-10 h-10 rounded-xl object-cover" alt="{{ $product->name }}">
                                            @else
                                                <x-icon name="photo" class="w-5 h-5 text-secondary opacity-40" />
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p
                                                class="font-medium text-primary truncate group-hover:text-primary-green transition">
                                                {{ $product->name }}</p>
                                            <div class="flex items-center gap-2 text-xs text-secondary">
                                                <span>{{ $product->unit->symbol ?? '—' }}</span>
                                                @if ($product->isLowStock())
                                                    <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                                    <span class="text-amber-600 font-medium">Low stock</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-mono-num text-sm text-secondary">
                                        <div class="font-medium">{{ $product->sku }}</div>
                                        @if ($product->barcode)
                                            <div class="text-xs text-secondary opacity-60 font-mono-num">
                                                {{ $product->barcode }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-primary-green-light/30 text-xs font-medium text-secondary">
                                        <x-icon name="folder" class="w-3 h-3" />
                                        {{ $product->category->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono-num text-secondary">
                                    {{ $product->costPrice()->formatted() }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono-num font-semibold text-primary">
                                    {{ $product->sellingPrice()->formatted() }}
                                    <span class="text-xs text-secondary font-normal block">
                                        {{ $product->costPrice()->amount() > 0 ? round((($product->sellingPrice()->amount() - $product->costPrice()->amount()) / $product->costPrice()->amount()) * 100, 1) : 0 }}%
                                        margin
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono-num">
                                    @if ($product->isLowStock())
                                        <x-badge color="warning" class="font-semibold">
                                            <span class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                {{ $stock }}
                                            </span>
                                        </x-badge>
                                    @elseif ($stock == 0)
                                        <x-badge color="danger">Out of stock</x-badge>
                                    @else
                                        <span class="text-primary font-medium">{{ $stock }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($product->status === 'active')
                                        <x-badge color="success">
                                            <span class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        </x-badge>
                                    @elseif ($product->status === 'inactive')
                                        <x-badge color="gray">Inactive</x-badge>
                                    @else
                                        <x-badge color="danger">Discontinued</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-1">
                                        @can('update', $product)
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                                class="p-1.5 rounded-lg text-secondary hover:bg-primary-green-light hover:text-primary-green transition"
                                                title="Edit">
                                                <x-icon name="pencil" class="w-4 h-4" />
                                            </a>
                                        @endcan
                                        @can('delete', $product)
                                            <button type="button" data-modal-target="delete-product-{{ $product->id }}"
                                                class="p-1.5 rounded-lg text-secondary hover:bg-red-50 hover:text-red-600 transition"
                                                title="Delete">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>

                                            <x-modal id="delete-product-{{ $product->id }}" title="Delete Product">
                                                <div class="flex items-start gap-4">
                                                    <div
                                                        class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                                                        <x-icon name="alert-triangle" class="w-6 h-6" />
                                                    </div>
                                                    <div>
                                                        <p class="text-sm text-secondary">
                                                            Are you sure you want to delete <strong
                                                                class="text-primary">{{ $product->name }}</strong>?
                                                        </p>
                                                        <p class="text-xs text-secondary opacity-60 mt-1">
                                                            This action will hide it from the catalog but preserve historical
                                                            sales records.
                                                        </p>
                                                        <div
                                                            class="mt-2 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                                            <p
                                                                class="text-xs text-amber-700 dark:text-amber-300 flex items-center gap-1.5">
                                                                <x-icon name="info" class="w-3.5 h-3.5" />
                                                                Current stock: {{ $product->totalStock() }} units
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                                    class="mt-4 flex justify-end gap-2">
                                                    @csrf @method('DELETE')
                                                    <button type="button"
                                                        data-modal-close="delete-product-{{ $product->id }}"
                                                        class="rounded-xl border border-theme text-sm font-medium px-5 py-2 text-secondary hover:bg-primary-green-light hover:text-primary transition">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                        class="rounded-xl bg-red-600 hover:bg-red-700 text-sm font-medium px-5 py-2 text-white shadow-sm hover:shadow-md transition flex items-center gap-2">
                                                        <x-icon name="trash" class="w-4 h-4" />
                                                        Delete Product
                                                    </button>
                                                </form>
                                            </x-modal>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-20 h-20 rounded-2xl bg-primary-green-light/20 flex items-center justify-center mb-4">
                                            <x-icon name="package" class="w-10 h-10 text-secondary opacity-30" />
                                        </div>
                                        <p class="text-lg font-medium text-primary">No products found</p>
                                        <p class="text-sm text-secondary mt-1">Try adjusting your filters or add your first
                                            product</p>
                                        @can('create', \App\Models\Product::class)
                                            <a href="{{ route('admin.products.create') }}"
                                                class="inline-flex items-center gap-2 mt-4 rounded-xl bg-primary-green hover:bg-primary-green-dark text-white text-sm font-medium px-5 py-2.5 transition shadow-sm hover:shadow-md">
                                                <x-icon name="plus" class="w-4 h-4" />
                                                Add Product
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="border-t border-theme px-6 py-4 flex items-center justify-between">
                    <div class="text-sm text-secondary">
                        Showing <span class="font-medium text-primary">{{ $products->firstItem() ?? 0 }}</span>
                        to <span class="font-medium text-primary">{{ $products->lastItem() ?? 0 }}</span>
                        of <span class="font-medium text-primary">{{ $products->total() }}</span> results
                    </div>
                    <div>
                        {{ $products->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
