<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Route is already protected by role:admin middleware
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'role' => ['required', Rule::in(['viewer', 'analyst', 'admin'])],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Role must be one of: viewer, analyst, admin.',
        ];
    }
}