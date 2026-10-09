<?php

namespace App\Http\Requests\Api\Billing;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateInvoiceRequest extends FormRequest
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
            'appointment_id' => [
                'required',
                'integer',
                Rule::exists('appointments', 'id')->where('clinic_id', $this->user()?->clinic_id),
                Rule::unique('invoices', 'appointment_id'),
            ],
        ];
    }
}
