<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
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