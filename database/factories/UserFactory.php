<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'), // password
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'role' => fake()->randomElement(['customer', 'provider', 'admin']),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function provider(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'provider',
        ]);
    }
    public function customer(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'customer',
        ]);
    }
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
        ]);
    }
}
