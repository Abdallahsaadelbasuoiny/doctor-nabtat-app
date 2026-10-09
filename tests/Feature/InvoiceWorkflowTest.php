<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\User;

it('generates a pending invoice from the appointment clinic fee', function () {
    $clinic = Clinic::factory()->create(['fees' => 1250]);
    $patient = Patient::factory()->for($clinic)->create();
    $user = User::factory()->for($clinic)->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => User::factory()->for($clinic)->doctor()->create()->id,
    ]);

    $this->actingAs($user)
        ->postJson('/api/invoices/generate', ['appointment_id' => $appointment->id])
        ->assertCreated()
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonPath('data.amount', '1250.00')
        ->assertJsonPath('data.discount', '0.00')
        ->assertJsonPath('data.net_total', '1250.00');

    $this->assertDatabaseHas('invoices', [
        'appointment_id' => $appointment->id,
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'amount' => 1250,
        'status' => 'pending',
    ]);
});

it('applies a discount up to the invoice amount and reports the net total', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $user = User::factory()->for($clinic)->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => User::factory()->for($clinic)->doctor()->create()->id,
    ]);
    $invoice = Invoice::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'appointment_id' => $appointment->id,
        'amount' => 1000,
    ]);

    $this->actingAs($user)
        ->patchJson("/api/invoices/{$invoice->id}/discount", ['discount' => '125.50'])
        ->assertOk()
        ->assertJsonPath('data.discount', '125.50')
        ->assertJsonPath('data.net_total', '874.50');

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'discount' => '125.50',
    ]);
});

it('rejects a discount above the invoice amount without changing the invoice', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $user = User::factory()->for($clinic)->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => User::factory()->for($clinic)->doctor()->create()->id,
    ]);
    $invoice = Invoice::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'appointment_id' => $appointment->id,
        'amount' => 1000,
    ]);

    $this->actingAs($user)
        ->patchJson("/api/invoices/{$invoice->id}/discount", ['discount' => '1000.01'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('discount');

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'discount' => '0.00',
        'status' => 'pending',
    ]);
});

it('records payment method and amount while marking the invoice paid', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $user = User::factory()->for($clinic)->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => User::factory()->for($clinic)->doctor()->create()->id,
    ]);
    $invoice = Invoice::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'appointment_id' => $appointment->id,
        'amount' => 1000,
        'discount' => 100,
    ]);

    $this->actingAs($user)
        ->patchJson("/api/invoices/{$invoice->id}/pay", ['method' => 'card'])
        ->assertOk()
        ->assertJsonPath('data.status', 'paid')
        ->assertJsonPath('data.payment_method', 'card')
        ->assertJsonPath('data.net_total', '900.00')
        ->assertJsonPath('data.payments.0.amount', '900.00')
        ->assertJsonPath('data.payments.0.method', 'card');

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'paid',
        'payment_method' => 'card',
    ]);
    $this->assertDatabaseHas('payments', [
        'invoice_id' => $invoice->id,
        'clinic_id' => $clinic->id,
        'received_by_user_id' => $user->id,
        'amount' => '900.00',
        'method' => 'card',
    ]);
});

it('does not allow users to access invoices belonging to another clinic', function () {
    $clinic = Clinic::factory()->create();
    $patient = Patient::factory()->for($clinic)->create();
    $appointment = Appointment::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => User::factory()->for($clinic)->doctor()->create()->id,
    ]);
    $invoice = Invoice::factory()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'appointment_id' => $appointment->id,
    ]);
    $otherClinicUser = User::factory()->create();

    $this->actingAs($otherClinicUser)
        ->patchJson("/api/invoices/{$invoice->id}/pay", ['method' => 'cash'])
        ->assertNotFound();

    $this->assertDatabaseHas('invoices', [
        'id' => $invoice->id,
        'status' => 'pending',
        'payment_method' => null,
    ]);
});
