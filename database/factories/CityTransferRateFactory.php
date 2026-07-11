<?php

namespace Database\Factories;

use App\Models\CityTransferRate;
use App\Models\TransferLocation;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CityTransferRate>
 */
class CityTransferRateFactory extends Factory
{
    protected $model = CityTransferRate::class;

    public function definition(): array
    {
        return [
            'from_location_id' => TransferLocation::factory(),
            'to_location_id'   => TransferLocation::factory(),
            'vehicle_type_id'  => VehicleType::factory(),
            'fare_type'        => $this->faker->randomElement(['fixed', 'per_km', 'per_hr']),
            'price'            => $this->faker->randomFloat(2, 50, 500),
            'currency'         => 'AED',
            'notes'            => null,
            'is_active'        => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
