<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('products.update');
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:64', 'alpha_dash', Rule::unique('products', 'sku')->ignore($productId)],
            'barcode' => ['nullable', 'string', 'max:64', Rule::unique('products', 'barcode')->ignore($productId)],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')],
            'unit_id' => ['required', 'integer', Rule::exists('units', 'id')],
            'description' => ['nullable', 'string', 'max:5000'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'cost_price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'tax_rate_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_tax_inclusive_price' => ['boolean'],
            'min_stock_level' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive', 'discontinued'])],
            'track_stock' => ['boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $sellingPrice = (float) $this->input('selling_price', 0);
            $costPrice = (float) $this->input('cost_price', 0);
            $confirmed = $this->boolean('confirm_below_cost');

            if ($sellingPrice < $costPrice && !$confirmed) {
                $validator->errors()->add(
                    'selling_price',
                    'Selling price is below cost price. Check "Confirm" if this is intentional.'
                );
            }
        });
    }
}
