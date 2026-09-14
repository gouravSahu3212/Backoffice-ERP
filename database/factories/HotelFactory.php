<?php

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\TransferLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hotel>
 */
class HotelFactory extends Factory
{
    protected $model = Hotel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' Hotel',
            'location_id' => TransferLocation::factory(),
            'address' => $this->faker->address(),
            'description' => $this->faker->paragraph(),
            'terms_and_conditions' => $this->faker->paragraph(),
            'star_rating' => $this->faker->numberBetween(3, 5),
            'amenities' => ['WiFi', 'Pool', 'Gym', 'Restaurant'],
            'is_active' => true,
            'is_featured' => $this->faker->boolean(),
        ];
    }
}
