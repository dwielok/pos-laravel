<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $user
            ? $this->user()->can('users.update')
            : $this->user()->can('users.create');
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $isCreating = !$userId;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => [$isCreating ? 'required' : 'nullable', Password::defaults()],
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'is_active' => ['boolean'],
        ];
    }
}
