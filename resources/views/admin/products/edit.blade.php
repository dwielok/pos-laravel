@extends('layouts.admin')

@section('page-title', 'Edit Product')
@section('breadcrumb', 'Edit Product Details')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-2 text-sm text-secondary">
                        <a href="{{ route('admin.products.index') }}"
                            class="hover:text-primary-green transition flex items-center gap-1">
                            <x-icon name="chevron-left" class="w-3 h-3" />
                            Back to Products
                        </a>
                        <span class="w-1 h-1 rounded-full bg-secondary opacity-30"></span>
                        <span class="flex items-center gap-1">
                            Editing: <span class="font-medium text-primary">{{ $product->name }}</span>
                        </span>
                        <span class="w-1 h-1 rounded-full bg-secondary opacity-30"></span>
                        <span class="font-mono-num text-xs">{{ $product->sku }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                {{-- Status Badge --}}
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                    @if ($product->status === 'active') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                    @elseif($product->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-800/30 dark:text-gray-300
                    @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 @endif">
                    <span
                        class="w-1.5 h-1.5 rounded-full
                        @if ($product->status === 'active') bg-emerald-500 animate-pulse
                        @elseif($product->status === 'inactive') bg-gray-400
                        @else bg-red-500 @endif">
                    </span>
                    {{ ucfirst($product->status) }}
                </span>

                {{-- Stock Badge --}}
                @php $stock = $product->totalStock(); @endphp
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium
                    @if ($stock == 0) bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                    @elseif($product->isLowStock()) bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                    @else bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 @endif">
                    <x-icon name="inbox" class="w-3 h-3" />
                    {{ $stock }} units in stock
                </span>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
            id="product-form">
            @method('PUT')
            @include('admin.products._form')

            {{-- Form Actions --}}
            <div
                class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 bg-card rounded-2xl border border-theme p-4 shadow-sm">
                <div class="flex items-center gap-4 text-sm text-secondary">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Last updated: {{ $product->updated_at->diffForHumans() }}
                    </span>
                    <span class="inline-block w-1 h-1 rounded-full bg-secondary opacity-30"></span>
                    <span>Created: {{ $product->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.products.index') }}"
                        class="flex-1 sm:flex-none rounded-xl border border-theme text-sm font-medium px-6 py-2.5 text-secondary hover:bg-primary-green-light hover:text-primary transition text-center">
                        Cancel
                    </a>
                    <button type="submit"
                        class="flex-1 sm:flex-none rounded-xl bg-primary-green hover:bg-primary-green-dark text-sm font-medium px-6 py-2.5 text-white shadow-sm hover:shadow-md transition flex items-center justify-center gap-2 group">
                        <x-icon name="check" class="w-4 h-4 group-hover:scale-110 transition-transform duration-300" />
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @include('admin.products._form-scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-save indicator (optional)
            let formChanged = false;
            const form = document.getElementById('product-form');

            // Track changes
            form.querySelectorAll('input, select, textarea').forEach(element => {
                element.addEventListener('change', function() {
                    formChanged = true;
                });
            });

            // Warning before leaving with unsaved changes
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                }
            });

            // Reset change tracking on form submit
            form.addEventListener('submit', function() {
                formChanged = false;
            });
        });
    </script>
@endpush
