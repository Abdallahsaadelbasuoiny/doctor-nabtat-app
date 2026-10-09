<?php

namespace App\Http\Requests\Api\Clinical;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
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
            'notes' => ['nullable', 'string', 'max:10000'],
            'medications' => ['required', 'array', 'min:1'],
            'medications.*' => ['required', 'array:name,dosage,frequency,duration,instructions'],
            'medications.*.name' => ['required', 'string', 'max:255'],
            'medications.*.dosage' => ['required', 'string', 'max:100'],
            'medications.*.frequency' => ['required', 'string', 'max:100'],
            'medications.*.duration' => ['required', 'string', 'max:100'],
            'medications.*.instructions' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
