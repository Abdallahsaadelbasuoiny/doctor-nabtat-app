<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'patient_id' => Patient::factory(),
            'appointment_id' => Appointment::factory(),
            'amount' => 500,
            'discount' => 0,
            'payment_method' => null,
            'status' => 'pending',
        ];
    }
}
