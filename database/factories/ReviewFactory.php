<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::where('status', 'completed')->inRandomOrder()->first();

        if (!$booking) {
            $provider = Provider::inRandomOrder()->first() ?? Provider::factory()->create();
            $user = User::factory()->customer()->create();

            return [
                'user_id' => $user->id,
                'provider_id' => $provider->id,
                'rating' => fake()->numberBetween(1, 5),
                'comment' => fake()->paragraph(),
            ];
        }

        return [
            'user_id' => $booking->user_id,
            'provider_id' => $booking->provider_id,
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->paragraph(),
        ];
    }
}
