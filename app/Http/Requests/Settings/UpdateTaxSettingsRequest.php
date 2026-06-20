<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.tax');
    }

    public function rules(): array
    {
        return [
            'default_rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'label' => ['required', 'string', 'max:50'],
            'prices_include_tax' => ['boolean'],
        ];
    }
}
