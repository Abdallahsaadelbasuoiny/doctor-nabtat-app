<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    protected $model = User::class;

    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'receptionist',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function doctor(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => 'doctor',
            'specialty' => fake()->jobTitle(),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}
