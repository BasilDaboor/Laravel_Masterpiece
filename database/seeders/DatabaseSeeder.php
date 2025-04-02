<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Provider;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $services = Service::factory(10)->create();

        $customers = User::factory(20)->customer()->create();

        $providers = [];
        for ($i = 0; $i < 15; $i++) {
            $user = User::factory()->provider()->create();
            $providers[] = Provider::factory()->create([
                'user_id' => $user->id,
                'service_id' => $services->random()->id,
            ]);
        }



        foreach ($customers as $customer) {
            $bookingsCount = rand(0, 4);
            for ($i = 0; $i < $bookingsCount; $i++) {
                $provider = $providers[array_rand($providers)];
                $status = fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']);

                $booking = Booking::factory()->create([
                    'user_id' => $customer->id,
                    'provider_id' => $provider->id,
                    'service_id' => $provider->service_id,
                    'status' => $status,
                ]);

                // Add reviews for completed bookings
                if ($status === 'completed') {
                    Review::factory()->create([
                        'user_id' => $customer->id,
                        'provider_id' => $provider->id,
                    ]);

                    // Update provider rating
                    $avgRating = Review::where('provider_id', $provider->id)->avg('rating');
                    $provider->update(['rating' => $avgRating]);
                }
            }
        }
    }
}
