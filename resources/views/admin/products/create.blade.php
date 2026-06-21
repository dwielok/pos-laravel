@extends('layouts.admin')

@section('page-title', 'Add Product')
@section('breadcrumb', 'Create New Product')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div>
                    {{-- <h2 class="text-xl font-semibold text-primary">Add New Product</h2> --}}
                    <div class="flex items-center gap-2 text-sm text-secondary">
                        <a href="{{ route('admin.products.index') }}"
                            class="hover:text-primary-green transition flex items-center gap-1">
                            <x-icon name="chevron-left" class="w-3 h-3" />
                            Back to Products
                        </a>
                        <span class="w-1 h-1 rounded-full bg-secondary opacity-30"></span>
                        <span>Create a new product in your catalog</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs text-secondary bg-primary-green-light/20 px-3 py-1.5 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ \App\Models\Product::count() }} products in catalog
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" id="product-form">
            @include('admin.products._form')

            {{-- Form Actions --}}
            <div
                class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 bg-card rounded-2xl border border-theme p-4 shadow-sm">
                <div class="text-sm text-secondary">
                    <span class="font-medium text-primary">*</span> Required fields
                    <span class="inline-block w-1 h-1 rounded-full bg-secondary opacity-30 mx-2"></span>
                    All changes are saved immediately
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.products.index') }}"
                        class="flex-1 sm:flex-none rounded-xl border border-theme text-sm font-medium px-6 py-2.5 text-secondary hover:bg-primary-green-light hover:text-primary transition text-center">
                        Cancel
                    </a>
                    <button type="submit"
                        class="flex-1 sm:flex-none rounded-xl bg-primary-green hover:bg-primary-green-dark text-sm font-medium px-6 py-2.5 text-white shadow-sm hover:shadow-md transition flex items-center justify-center gap-2 group">
                        <x-icon name="plus" class="w-4 h-4 group-hover:rotate-90 transition-transform duration-300" />
                        Create Product
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
            // Image upload preview
            const imageInput = document.getElementById('image-input');
            const imagePreview = document.getElementById('image-preview');
            const imagePlaceholder = document.getElementById('image-placeholder');
            const previewWrap = document.getElementById('image-preview-wrap');
            const removeImageBtn = document.getElementById('remove-image');

            // Click on preview to trigger file input
            if (previewWrap) {
                previewWrap.addEventListener('click', function(e) {
                    if (e.target.tagName !== 'BUTTON') {
                        imageInput.click();
                    }
                });
            }

            // Handle file selection
            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            if (imagePreview) {
                                imagePreview.src = event.target.result;
                                imagePreview.classList.remove('hidden');
                            }
                            if (imagePlaceholder) {
                                imagePlaceholder.classList.add('hidden');
                            }
                            if (removeImageBtn) {
                                removeImageBtn.classList.remove('hidden');
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Remove image
            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', function() {
                    if (imageInput) {
                        imageInput.value = '';
                    }
                    if (imagePreview) {
                        imagePreview.src = '';
                        imagePreview.classList.add('hidden');
                    }
                    if (imagePlaceholder) {
                        imagePlaceholder.classList.remove('hidden');
                    }
                    removeImageBtn.classList.add('hidden');
                });
            }

            // Barcode generator
            const generateBtn = document.getElementById('generate-barcode');
            const barcodeInput = document.getElementById('barcode-input');

            if (generateBtn && barcodeInput) {
                generateBtn.addEventListener('click', function() {
                    const barcode = 'BC-' + Date.now().toString().slice(-10);
                    barcodeInput.value = barcode;
                    barcodeInput.dispatchEvent(new Event('input'));
                });
            }

            // Price margin calculator
            const costPrice = document.getElementById('cost_price');
            const sellingPrice = document.getElementById('selling_price');
            const marginIndicator = document.getElementById('margin-indicator');
            const marginPercentage = document.getElementById('margin-percentage');
            const marginBar = document.getElementById('margin-bar');
            const belowCostWarning = document.getElementById('below-cost-warning');

            function calculateMargin() {
                const cost = parseFloat(costPrice?.value) || 0;
                const selling = parseFloat(sellingPrice?.value) || 0;

                if (cost > 0 && selling > 0) {
                    marginIndicator.classList.remove('hidden');
                    const margin = ((selling - cost) / selling) * 100;
                    const percentage = Math.max(0, Math.min(100, margin));

                    marginPercentage.textContent = margin.toFixed(1) + '%';
                    marginBar.style.width = percentage + '%';

                    if (margin < 0) {
                        marginBar.classList.remove('bg-primary-green');
                        marginBar.classList.add('bg-red-500');
                        marginPercentage.classList.remove('text-primary');
                        marginPercentage.classList.add('text-red-600');
                        belowCostWarning.classList.remove('hidden');
                    } else {
                        marginBar.classList.remove('bg-red-500');
                        marginBar.classList.add('bg-primary-green');
                        marginPercentage.classList.remove('text-red-600');
                        marginPercentage.classList.add('text-primary');
                        belowCostWarning.classList.add('hidden');
                    }

                    // Color coding for margin quality
                    if (margin >= 40) {
                        marginIndicator.className =
                            'rounded-xl px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800';
                    } else if (margin >= 20) {
                        marginIndicator.className =
                            'rounded-xl px-4 py-3 bg-primary-green-light/20 border border-primary-green/20';
                    } else if (margin >= 0) {
                        marginIndicator.className =
                            'rounded-xl px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800';
                    } else {
                        marginIndicator.className =
                            'rounded-xl px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800';
                    }
                } else {
                    marginIndicator.classList.add('hidden');
                    belowCostWarning.classList.add('hidden');
                }
            }

            if (costPrice && sellingPrice) {
                costPrice.addEventListener('input', calculateMargin);
                sellingPrice.addEventListener('input', calculateMargin);
                calculateMargin();
            }
        });
    </script>
@endpush
