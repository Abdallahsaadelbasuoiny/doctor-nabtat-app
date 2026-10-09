<?php

namespace App\Http\Controllers\Api\Clinical;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Clinical\StoreConsultationRequest;
use App\Http\Resources\ConsultationResource;
use App\Models\Appointment;
use App\Models\Consultation;
use Illuminate\Http\JsonResponse;

class ConsultationController extends Controller
{
    public function store(StoreConsultationRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $appointment = Appointment::query()
            ->where('clinic_id', $user->clinic_id)
            ->where('doctor_id', $user->id)
            ->with('patient')
            ->findOrFail($data['appointment_id']);

        $consultation = Consultation::query()->create([
            'clinic_id' => $appointment->clinic_id,
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $user->id,
            'diagnosis' => $data['diagnosis'],
            'symptoms' => $data['symptoms'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return (new ConsultationResource($consultation))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
