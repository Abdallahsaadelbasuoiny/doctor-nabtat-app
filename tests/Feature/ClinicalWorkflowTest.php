<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;

it('rejects consultation authoring by users who are not doctors', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $doctor = User::factory()->for($clinic)->doctor()->create();
    $receptionist = User::factory()->for($clinic)->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $this->actingAs($receptionist)
        ->postJson('/api/consultations', [
            'appointment_id' => $appointment->id,
            'diagnosis' => 'Seasonal allergy',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('consultations', 0);
});

it('stores a consultation for the authenticated doctor and linked appointment', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $doctor = User::factory()->for($clinic)->doctor()->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $this->actingAs($doctor)
        ->postJson('/api/consultations', [
            'appointment_id' => $appointment->id,
            'diagnosis' => 'Seasonal allergy',
            'symptoms' => 'Sneezing and itchy eyes',
            'notes' => 'Follow up in one week',
        ])
        ->assertCreated()
        ->assertJsonPath('data.doctor_id', $doctor->id)
        ->assertJsonPath('data.patient_id', $patient->id)
        ->assertJsonPath('data.diagnosis', 'Seasonal allergy');

    $this->assertDatabaseHas('consultations', [
        'appointment_id' => $appointment->id,
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'diagnosis' => 'Seasonal allergy',
    ]);
});

it('stores a prescription and all nested medication items together', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $doctor = User::factory()->for($clinic)->doctor()->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);
    $consultation = Consultation::factory()->create([
        'clinic_id' => $clinic->id,
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $this->actingAs($doctor)
        ->postJson("/api/consultations/{$consultation->id}/prescriptions", [
            'notes' => 'Take after meals',
            'medications' => [
                ['name' => 'Medicine A', 'dosage' => '500 mg', 'frequency' => 'Twice daily', 'duration' => '5 days'],
                ['name' => 'Medicine B', 'dosage' => '10 mg', 'frequency' => 'Once daily', 'duration' => '7 days'],
            ],
        ])
        ->assertCreated()
        ->assertJsonPath('data.medications.0.name', 'Medicine A')
        ->assertJsonPath('data.medications.1.name', 'Medicine B');

    $this->assertDatabaseCount('prescriptions', 1);
    $this->assertDatabaseCount('prescription_items', 2);
});

it('rolls back a prescription when a nested medication insert fails', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $doctor = User::factory()->for($clinic)->doctor()->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);
    $consultation = Consultation::factory()->create([
        'clinic_id' => $clinic->id,
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    PrescriptionItem::creating(function (PrescriptionItem $item): void {
        if ($item->name === 'Force transaction rollback') {
            throw new RuntimeException('Simulated medication insert failure.');
        }
    });

    try {
        $this->actingAs($doctor)->postJson("/api/consultations/{$consultation->id}/prescriptions", [
            'medications' => [
                ['name' => 'Medicine A', 'dosage' => '500 mg', 'frequency' => 'Twice daily', 'duration' => '5 days'],
                ['name' => 'Force transaction rollback', 'dosage' => '10 mg', 'frequency' => 'Once daily', 'duration' => '7 days'],
            ],
        ]);
    } catch (RuntimeException) {
        // The injected failure is expected; database state below verifies rollback.
    } finally {
        PrescriptionItem::flushEventListeners();
    }

    $this->assertDatabaseCount('prescriptions', 0);
    $this->assertDatabaseCount('prescription_items', 0);
});

it('returns a print-ready prescription payload with ordered medication details', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $doctor = User::factory()->for($clinic)->doctor()->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);
    $consultation = Consultation::factory()->create([
        'clinic_id' => $clinic->id,
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'diagnosis' => 'Seasonal allergy',
    ]);
    $prescription = Prescription::factory()->create([
        'consultation_id' => $consultation->id,
        'doctor_id' => $doctor->id,
    ]);
    $prescription->items()->create([
        'name' => 'Medicine A',
        'dosage' => '500 mg',
        'frequency' => 'Twice daily',
        'duration' => '5 days',
        'sort_order' => 0,
    ]);

    $this->actingAs($doctor)
        ->getJson("/api/prescriptions/{$prescription->id}/print")
        ->assertOk()
        ->assertJsonPath('data.clinic.name', $clinic->name)
        ->assertJsonPath('data.patient.name', $patient->name)
        ->assertJsonPath('data.doctor.name', $doctor->name)
        ->assertJsonPath('data.consultation.diagnosis', 'Seasonal allergy')
        ->assertJsonPath('data.medications.0.name', 'Medicine A');
});

it('requires an authenticated user for clinical and prescription endpoints', function () {
    $this->postJson('/api/consultations', [])->assertUnauthorized();
});

it('rejects prescriptions written by users who are not doctors', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $doctor = User::factory()->for($clinic)->doctor()->create();
    $receptionist = User::factory()->for($clinic)->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);
    $consultation = Consultation::factory()->create([
        'clinic_id' => $clinic->id,
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
    ]);

    $this->actingAs($receptionist)
        ->postJson("/api/consultations/{$consultation->id}/prescriptions", [
            'medications' => [
                ['name' => 'Medicine A', 'dosage' => '500 mg', 'frequency' => 'Twice daily', 'duration' => '5 days'],
            ],
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('prescriptions', 0);
    $this->assertDatabaseCount('prescription_items', 0);
});
