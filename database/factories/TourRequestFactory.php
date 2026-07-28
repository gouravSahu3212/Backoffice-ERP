<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourRequest>
 */
class TourRequestFactory extends Factory
{
    private static array $statuses = ['new', 'contacted', 'confirmed', 'closed'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pax = fake()->numberBetween(1, 8);
        $agentPrice = fake()->numberBetween(500, 5000);

        return [
            'request_reference' => 'TR-'.strtolower(Str::random(8)),
            'tour_id' => Tour::factory(),
            'agent_id' => User::factory(),
            'customer_name' => fake()->name(),
            'date_of_birth' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'passport_number' => strtoupper(fake()->bothify('??#######')),
            'departure_date' => fake()->dateTimeBetween('now', '+2 years')->format('Y-m-d'),
            'pax' => $pax,
            'total_price' => $agentPrice * $pax,
            'currency' => fake()->randomElement(['USD', 'SAR']),
            'status' => fake()->randomElement(self::$statuses),
        ];
    }
}
