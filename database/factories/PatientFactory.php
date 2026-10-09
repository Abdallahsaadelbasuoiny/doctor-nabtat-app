<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'file_number' => fake()->unique()->numerify('PAT-#####'),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'date_of_birth' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'address' => fake()->address(),
        ];
    }
}
