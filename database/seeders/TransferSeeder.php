<?php

namespace Database\Seeders;

use App\Models\CityTransferRate;
use App\Models\TransferLocation;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class TransferSeeder extends Seeder
{
    public function run(): void
    {
        // Default UAE cities
        $cities = [
            'Dubai',
            'Abu Dhabi',
            'Sharjah',
            'Ajman',
            'Ras Al Khaimah',
            'Fujairah',
            'Al Ain',
        ];

        foreach ($cities as $city) {
            TransferLocation::firstOrCreate(
                ['name' => $city, 'type' => 'city'],
                ['is_active' => true]
            );
        }

        // Default vehicle types
        $vehicles = ['Sedan', 'SUV', 'Van', 'Bus'];

        foreach ($vehicles as $vehicle) {
            VehicleType::firstOrCreate(
                ['name' => $vehicle],
                ['is_active' => true]
            );
        }

        // Sample city-to-city rates
        $dubai    = TransferLocation::where('name', 'Dubai')->first();
        $abuDhabi = TransferLocation::where('name', 'Abu Dhabi')->first();
        $sharjah  = TransferLocation::where('name', 'Sharjah')->first();
        $ajman    = TransferLocation::where('name', 'Ajman')->first();
        $alAin    = TransferLocation::where('name', 'Al Ain')->first();

        $sedan = VehicleType::where('name', 'Sedan')->first();
        $suv   = VehicleType::where('name', 'SUV')->first();
        $van   = VehicleType::where('name', 'Van')->first();

        $sampleRates = [
            [$dubai, $abuDhabi, $sedan, 250],
            [$dubai, $abuDhabi, $suv, 350],
            [$dubai, $sharjah, $sedan, 120],
            [$dubai, $sharjah, $van, 200],
            [$abuDhabi, $alAin, $sedan, 180],
            [$dubai, $ajman, $sedan, 150],
        ];

        foreach ($sampleRates as [$from, $to, $vehicle, $price]) {
            CityTransferRate::firstOrCreate(
                [
                    'from_location_id' => $from->id,
                    'to_location_id'   => $to->id,
                    'vehicle_type_id'  => $vehicle->id,
                ],
                [
                    'fare_type'  => 'fixed',
                    'price'      => $price,
                    'currency'   => 'AED',
                    'is_active'  => true,
                ]
            );
        }
    }
}
