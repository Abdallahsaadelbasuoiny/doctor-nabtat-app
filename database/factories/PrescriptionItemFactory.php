<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrescriptionItem>
 */
class PrescriptionItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'prescription_id' => Prescription::factory(),
            'name' => fake()->word(),
            'dosage' => '500 mg',
            'frequency' => 'Twice daily',
            'duration' => '5 days',
            'instructions' => fake()->sentence(),
            'sort_order' => 0,
        ];
    }
}
