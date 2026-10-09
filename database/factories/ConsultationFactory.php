<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consultation>
 */
class ConsultationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'appointment_id' => Appointment::factory(),
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory()->doctor(),
            'diagnosis' => fake()->sentence(),
            'symptoms' => fake()->paragraph(),
            'notes' => fake()->paragraph(),
        ];
    }
}
