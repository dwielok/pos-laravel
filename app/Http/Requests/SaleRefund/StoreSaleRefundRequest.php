<?php

namespace App\Http\Requests\SaleRefund;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('refund', $this->route('sale'));
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:500'],
            'refund_method' => ['required', Rule::in(['cash', 'card', 'store_credit', 'other'])],

            // keyed by sale_item_id => quantity to refund
            'quantities' => ['required', 'array', 'min:1'],
            'quantities.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'quantities.required' => 'Select at least one item and quantity to refund.',
        ];
    }
}
