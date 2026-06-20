<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('pos.access');
    }

    public function rules(): array
    {
        return [
            'client_uuid' => ['nullable', 'uuid'],
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')],
            'warehouse_id' => ['required', 'integer', Rule::exists('warehouses', 'id')],
            'discount_type' => ['nullable', Rule::in(['fixed', 'percent'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.discount_type' => ['nullable', Rule::in(['fixed', 'percent'])],
            'items.*.discount_value' => ['nullable', 'numeric', 'min:0'],

            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', Rule::in(['cash', 'card', 'bank_transfer', 'e_wallet', 'store_credit', 'other'])],
            'payments.*.amount' => ['required', 'numeric', 'min:0'],
            'payments.*.reference_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Cart is empty.',
            'payments.required' => 'At least one payment method is required.',
        ];
    }
}
