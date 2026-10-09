<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'consultation_id' => Consultation::factory(),
            'doctor_id' => User::factory()->doctor(),
            'issued_at' => now(),
        ];
    }
}
