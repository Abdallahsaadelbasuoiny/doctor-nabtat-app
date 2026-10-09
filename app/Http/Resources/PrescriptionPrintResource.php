<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionPrintResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'issued_at' => $this->issued_at,
            'clinic' => [
                'id' => $this->consultation->clinic->id,
                'name' => $this->consultation->clinic->name,
                'address' => $this->consultation->clinic->address,
                'phone' => $this->consultation->clinic->phone,
            ],
            'patient' => [
                'id' => $this->consultation->patient->id,
                'name' => $this->consultation->patient->name,
                'file_number' => $this->consultation->patient->file_number,
            ],
            'doctor' => [
                'id' => $this->doctor->id,
                'name' => $this->doctor->name,
                'specialty' => $this->doctor->specialty,
            ],
            'consultation' => [
                'id' => $this->consultation->id,
                'diagnosis' => $this->consultation->diagnosis,
                'symptoms' => $this->consultation->symptoms,
                'notes' => $this->consultation->notes,
            ],
            'prescription' => $this->prescription,
        ];
    }
}
