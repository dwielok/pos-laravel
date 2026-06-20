<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the payload shape for a single offline sale being synced. Note
 * what is deliberately NOT validated here: we don't check that unit_price_cents
 * matches the product's current price, or that quantity is available in
 * stock -- those are exactly the checks the price-lock and
 * negative-stock-allowed requirements say we must NOT enforce at sync time.
 * This class validates SHAPE and basic sanity (positive integers, valid
 * enum values), not business-rule conformance against current server state.
 */
class SyncOfflineSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gated by 'auth' + 'register.session' middleware on the route
    }

    public function rules(): array
    {
        return [
            'client_uuid' => ['required', 'uuid'],
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')],
            'created_offline_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'subtotal_cents' => ['required', 'integer', 'min:0'],
            'discount_cents' => ['nullable', 'integer', 'min:0'],
            'discount_type' => ['nullable', Rule::in(['fixed', 'percent'])],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'tax_cents' => ['nullable', 'integer', 'min:0'],
            'tax_rate_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'total_cents' => ['required', 'integer', 'min:0'],
            'paid_cents' => ['required', 'integer', 'min:0'],
            'change_cents' => ['nullable', 'integer', 'min:0'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.product_name_snapshot' => ['nullable', 'string', 'max:255'],
            'items.*.product_sku_snapshot' => ['nullable', 'string', 'max:64'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price_cents' => ['required', 'integer', 'min:0'],
            'items.*.unit_cost_cents' => ['nullable', 'integer', 'min:0'],
            'items.*.discount_cents' => ['nullable', 'integer', 'min:0'],
            'items.*.discount_type' => ['nullable', Rule::in(['fixed', 'percent'])],
            'items.*.discount_value' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_cents' => ['nullable', 'integer', 'min:0'],
            'items.*.tax_rate_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.subtotal_cents' => ['required', 'integer', 'min:0'],
            'items.*.total_cents' => ['required', 'integer', 'min:0'],

            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', Rule::in(['cash', 'card', 'bank_transfer', 'e_wallet', 'store_credit', 'other', 'qris'])],
            'payments.*.amount_cents' => ['required', 'integer', 'min:0'],
            'payments.*.reference_number' => ['nullable', 'string', 'max:255'],
        ];
    }
}
