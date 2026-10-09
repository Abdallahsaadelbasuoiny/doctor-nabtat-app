<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Prescription;
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

it('stores the doctor-written prescription as one text field', function () {
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
            'prescription' => "Medicine A 500 mg, twice daily for 5 days.\nMedicine B 10 mg once daily for 7 days.",
        ])
        ->assertCreated()
        ->assertJsonPath('data.prescription', "Medicine A 500 mg, twice daily for 5 days.\nMedicine B 10 mg once daily for 7 days.");

    $this->assertDatabaseCount('prescriptions', 1);
});

it('returns a print-ready prescription payload with the free-text prescription', function () {
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
        'prescription' => 'Take the medicine as directed.',
    ]);

    $this->actingAs($doctor)
        ->getJson("/api/prescriptions/{$prescription->id}/print")
        ->assertOk()
        ->assertJsonPath('data.clinic.name', $clinic->name)
        ->assertJsonPath('data.patient.name', $patient->name)
        ->assertJsonPath('data.doctor.name', $doctor->name)
        ->assertJsonPath('data.consultation.diagnosis', 'Seasonal allergy')
        ->assertJsonPath('data.prescription', 'Take the medicine as directed.');
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
            'prescription' => 'Take the medicine as directed.',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('prescriptions', 0);
});
