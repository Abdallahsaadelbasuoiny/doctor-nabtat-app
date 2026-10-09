<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('-1 month', '+1 month');

        return [
            'clinic_id' => Clinic::factory(),
            'patient_id' => Patient::factory(),
            'doctor_id' => User::factory()->doctor(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+30 minutes'),
            'status' => 'confirmed',
            'source' => 'in_person',
            'slot_lock' => 0,
        ];
    }
}
