<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clinic>
 */
class ClinicFactory extends Factory
{
    protected $model = Clinic::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'subscription_plan' => 'basic',
            'status' => 'active',
            'fees' => 500,
        ];
    }
}
