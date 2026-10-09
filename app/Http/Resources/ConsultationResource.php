<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'clinic_id' => $this->clinic_id,
            'appointment_id' => $this->appointment_id,
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'diagnosis' => $this->diagnosis,
            'symptoms' => $this->symptoms,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
        ];
    }
}
