<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinancialRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Route is protected by role:admin,analyst middleware
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category' => ['required', 'string', 'max:100'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.min' => 'Amount must be greater than zero.',
            'amount.max' => 'Amount cannot exceed 99,999,999.99.',
            'type.in' => 'Type must be either: income or expense.',
            'date.before_or_equal' => 'Date cannot be in the future.',
        ];
    }
}