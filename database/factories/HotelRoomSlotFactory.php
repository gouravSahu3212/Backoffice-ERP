<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\HotelRoomSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HotelRoomSlot>
 */
class HotelRoomSlotFactory extends Factory
{
    protected $model = HotelRoomSlot::class;

    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'name' => $this->faker->randomElement(['Standard Room', 'Executive Room', 'Deluxe Suite', 'Family Room']),
            'capacity' => 2,
            'available_qty' => $this->faker->numberBetween(5, 30),
            'price_per_night' => $this->faker->randomElement([200, 280, 350, 420, 550]),
            'currency' => 'AED',
            'is_active' => true,
        ];
    }
}
