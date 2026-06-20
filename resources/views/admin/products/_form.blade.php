@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main fields --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4">
            <h3 class="font-semibold text-slate-900">Basic Information</h3>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Product Name <span
                        class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        SKU @if (!isset($product))
                            <span class="text-slate-400 font-normal">(auto-generated if blank)</span>
                        @endif
                    </label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                        class="w-full rounded-lg border-slate-300 text-sm font-mono-num focus:border-indigo-500 focus:ring-indigo-500 @error('sku') border-red-300 @enderror">
                    @error('sku')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Barcode</label>
                    <div class="relative">
                        <input type="text" name="barcode" id="barcode-input"
                            value="{{ old('barcode', $product->barcode ?? '') }}"
                            class="w-full rounded-lg border-slate-300 text-sm font-mono-num pr-9 focus:border-indigo-500 focus:ring-indigo-500 @error('barcode') border-red-300 @enderror">
                        <x-icon name="barcode"
                            class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    </div>
                    @error('barcode')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Category</label>
                    <select name="category_id"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">— None —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? null) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Unit <span
                            class="text-red-500">*</span></label>
                    <select name="unit_id" required
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select unit...</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" @selected(old('unit_id', $product->unit_id ?? null) == $unit->id)>
                                {{ $unit->name }} ({{ $unit->symbol }})
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description ?? '') }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4">
            <h3 class="font-semibold text-slate-900">Pricing</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Cost Price <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">{{ $currencySymbol ?? '$' }}</span>
                        <input type="number" step="0.01" min="0" name="cost_price" id="cost_price"
                            value="{{ old('cost_price', isset($product) ? $product->costPrice()->units() : '') }}"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm font-mono-num pl-7 focus:border-indigo-500 focus:ring-indigo-500 @error('cost_price') border-red-300 @enderror">
                    </div>
                    @error('cost_price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Selling Price <span
                            class="text-red-500">*</span></label>
                    <div class="relative">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">{{ $currencySymbol ?? '$' }}</span>
                        <input type="number" step="0.01" min="0" name="selling_price" id="selling_price"
                            value="{{ old('selling_price', isset($product) ? $product->sellingPrice()->units() : '') }}"
                            required
                            class="w-full rounded-lg border-slate-300 text-sm font-mono-num pl-7 focus:border-indigo-500 focus:ring-indigo-500 @error('selling_price') border-red-300 @enderror">
                    </div>
                    @error('selling_price')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div id="margin-indicator" class="hidden rounded-lg px-3 py-2 text-sm"></div>

            <div id="below-cost-warning" class="hidden rounded-lg bg-amber-50 border border-amber-200 px-3 py-2.5">
                <label class="flex items-start gap-2 text-sm text-amber-800">
                    <input type="checkbox" name="confirm_below_cost" value="1"
                        class="mt-0.5 rounded border-amber-400 text-amber-600 focus:ring-amber-500">
                    <span>Selling price is below cost price. I confirm this is intentional (e.g. clearance sale).</span>
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tax Rate (%) <span
                        class="text-slate-400 font-normal">— leave blank to use store default</span></label>
                <input type="number" step="0.01" min="0" max="100" name="tax_rate_percent"
                    value="{{ old('tax_rate_percent', $product->tax_rate_percent ?? '') }}"
                    class="w-full sm:w-48 rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    </div>

    {{-- Sidebar: image, stock, status --}}
    <div class="space-y-5">
        <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-3">
            <h3 class="font-semibold text-slate-900">Product Image</h3>
            <div id="image-preview-wrap"
                class="aspect-square rounded-lg border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden bg-slate-50">
                @if (isset($product) && $product->image_path)
                    <img id="image-preview" src="{{ asset('storage/' . $product->image_path) }}"
                        class="w-full h-full object-cover">
                @else
                    <div id="image-placeholder" class="text-center text-slate-400 p-4">
                        <x-icon name="photo" class="w-8 h-8 mx-auto mb-1" />
                        <p class="text-xs">No image selected</p>
                    </div>
                    <img id="image-preview" class="hidden w-full h-full object-cover">
                @endif
            </div>
            <input type="file" name="image" id="image-input" accept="image/png,image/jpeg,image/webp"
                class="text-sm w-full">
            @error('image')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @if (!isset($product))
            <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-3">
                <h3 class="font-semibold text-slate-900">Opening Stock</h3>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Warehouse</label>
                    <select name="warehouse_id"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">— Skip for now —</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected($warehouse->is_default)>{{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Initial Quantity</label>
                    <input type="number" min="0" name="initial_stock" value="{{ old('initial_stock', 0) }}"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4">
            <h3 class="font-semibold text-slate-900">Inventory & Status</h3>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Minimum Stock Level</label>
                <input type="number" min="0" name="min_stock_level"
                    value="{{ old('min_stock_level', $product->min_stock_level ?? 5) }}"
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-slate-500">Alerts trigger when stock falls to or below this number.</p>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="track_stock" value="1" @checked(old('track_stock', $product->track_stock ?? true))
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Track stock for this product
            </label>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status"
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="active" @selected(old('status', $product->status ?? 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $product->status ?? '') === 'inactive')>Inactive</option>
                    <option value="discontinued" @selected(old('status', $product->status ?? '') === 'discontinued')>Discontinued</option>
                </select>
            </div>
        </div>
    </div>
</div>
