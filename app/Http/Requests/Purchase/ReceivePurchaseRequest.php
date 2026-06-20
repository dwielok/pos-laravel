<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class ReceivePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('purchases.receive');
    }

    public function rules(): array
    {
        return [
            // keyed by purchase_item_id => quantity received now
            'received' => ['required', 'array', 'min:1'],
            'received.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'received.required' => 'Enter at least one received quantity.',
        ];
    }
}
