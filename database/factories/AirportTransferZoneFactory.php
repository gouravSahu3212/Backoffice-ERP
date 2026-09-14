<?php

namespace Database\Factories;

use App\Models\AirportTransferZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AirportTransferZone>
 */
class AirportTransferZoneFactory extends Factory
{
    protected $model = AirportTransferZone::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city().' Zone',
            'is_active' => true,
        ];
    }
}
