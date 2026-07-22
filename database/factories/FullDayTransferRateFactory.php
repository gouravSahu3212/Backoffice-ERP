<?php

namespace Database\Factories;

use App\Models\FullDayTransferRate;
use App\Models\TransferLocation;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FullDayTransferRate>
 */
class FullDayTransferRateFactory extends Factory
{
    protected $model = FullDayTransferRate::class;

    public function definition(): array
    {
        return [
            'from_location_id' => TransferLocation::factory(),
            'to_location_id' => TransferLocation::factory(),
            'vehicle_type_id' => VehicleType::factory(),
            'fare_type' => $this->faker->randomElement(['half_day', 'full_day']),
            'price' => $this->faker->randomFloat(2, 50, 1000),
            'currency' => 'AED',
            'notes' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
