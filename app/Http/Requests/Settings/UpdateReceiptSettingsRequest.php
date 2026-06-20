<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReceiptSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.receipt');
    }

    public function rules(): array
    {
        return [
            'footer_text' => ['nullable', 'string', 'max:500'],
            'show_logo' => ['boolean'],
            'paper_size' => ['required', Rule::in(['58mm', '80mm', 'a4'])],
        ];
    }
}
