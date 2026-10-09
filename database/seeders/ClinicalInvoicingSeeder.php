<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClinicalInvoicingSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::query()->firstOrCreate(
            ['email' => 'demo-clinic@example.test'],
            ['name' => 'Demo Clinic', 'fees' => 500, 'status' => 'active'],
        );

        $doctor = User::query()->firstOrCreate(
            ['email' => 'doctor@example.test'],
            [
                'clinic_id' => $clinic->id,
                'name' => 'Demo Doctor',
                'password' => 'password',
                'role' => 'doctor',
                'is_active' => true,
            ],
        );

        $patient = Patient::query()->firstOrCreate(
            ['clinic_id' => $clinic->id, 'file_number' => 'DEMO-0001'],
            ['name' => 'Demo Patient'],
        );

        $appointment = Appointment::query()->firstOrCreate(
            ['clinic_id' => $clinic->id, 'patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'starts_at' => now()->startOfHour()],
            ['ends_at' => now()->startOfHour()->addMinutes(30), 'status' => 'confirmed', 'source' => 'in_person'],
        );

        $consultation = Consultation::query()->firstOrCreate(
            ['appointment_id' => $appointment->id],
            ['clinic_id' => $clinic->id, 'patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'diagnosis' => 'Demo diagnosis'],
        );

        $prescription = Prescription::query()->firstOrCreate(
            ['consultation_id' => $consultation->id],
            ['doctor_id' => $doctor->id, 'prescription' => 'Demo medication, 500 mg twice daily for 5 days.'],
        );

        Invoice::query()->firstOrCreate(
            ['appointment_id' => $appointment->id],
            ['clinic_id' => $clinic->id, 'patient_id' => $patient->id, 'amount' => $clinic->fees, 'discount' => 0, 'status' => 'pending'],
        );
    }
}
