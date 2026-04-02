<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFinancialRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'sometimes' means the field is only validated if it is present.
            // This allows partial updates without requiring all fields every time.
            'amount' => ['sometimes', 'required', 'numeric', 'min:0.01', 'max:99999999.99'],
            'type' => ['sometimes', 'required', Rule::in(['income', 'expense'])],
            'category' => ['sometimes', 'required', 'string', 'max:100'],
            'date' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
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