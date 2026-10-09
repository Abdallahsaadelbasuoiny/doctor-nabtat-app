<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'invoice_id' => Invoice::factory(),
            'amount' => 500,
            'method' => 'cash',
            'paid_at' => now(),
        ];
    }
}
