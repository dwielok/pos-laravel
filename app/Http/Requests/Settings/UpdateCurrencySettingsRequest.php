<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurrencySettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.currency');
    }

    public function rules(): array
    {
        return [
            'symbol' => ['required', 'string', 'max:5'],
            'code' => ['required', 'string', 'size:3', 'alpha'],
            'position' => ['required', Rule::in(['before', 'after'])],
        ];
    }
}
