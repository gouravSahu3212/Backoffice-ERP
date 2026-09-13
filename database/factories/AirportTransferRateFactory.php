<?php

namespace Database\Factories;

use App\Models\AirportTransferRate;
use App\Models\AirportTransferZone;
use App\Models\TransferLocation;
use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AirportTransferRate>
 */
class AirportTransferRateFactory extends Factory
{
    protected $model = AirportTransferRate::class;

    public function definition(): array
    {
        return [
            'airport_id' => TransferLocation::factory(),
            'zone_id' => AirportTransferZone::factory(),
            'vehicle_type_id' => VehicleType::factory(),
            'transfer_type' => 'pickup',
            'fare_type' => 'fixed',
            'price' => $this->faker->numberBetween(100, 500),
            'currency' => 'AED',
            'is_active' => true,
        ];
    }
}
