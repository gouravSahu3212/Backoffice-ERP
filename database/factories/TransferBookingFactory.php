<?php

namespace Database\Factories;

use App\Models\TransferBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferBooking>
 */
class TransferBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_reference' => 'TB-'.fake()->unique()->numberBetween(1000, 9999),
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'guest_phone' => fake()->phoneNumber(),
            'pickup_location' => fake()->city().' Airport',
            'dropoff_location' => fake()->city().' Hotel',
            'vehicle_type' => fake()->randomElement(['Sedan', 'Van', 'Minibus']),
            'transfer_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'pickup_time' => fake()->time('H:i'),
            'passengers' => fake()->numberBetween(1, 6),
            'status' => fake()->randomElement(['pending', 'confirmed', 'cancelled']),
            'notes' => fake()->optional()->sentence(),
            'total_amount' => fake()->randomFloat(2, 80, 500),
            'currency' => 'SAR',
        ];
    }
}
