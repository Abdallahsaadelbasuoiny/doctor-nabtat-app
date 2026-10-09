<?php

namespace App\Http\Requests\Api\Billing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DiscountInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'discount' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
        ];
    }
}
