<?php

namespace Database\Seeders;

use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class VehicleModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        VehicleModel::truncate();
        VehicleType::truncate();
        Schema::enableForeignKeyConstraints();

        $data = [
            'Sedan' => [
                'Toyota Camry',
                'Honda Accord',
                'Nissan Altima',
                'Hyundai Elantra',
                'Toyota Corolla',
                'Honda Civic',
                'Mazda 6',
            ],
            'SUV' => [
                'Toyota Land Cruiser',
                'Nissan Patrol',
                'Chevrolet Tahoe',
                'GMC Yukon',
                'Mitsubishi Pajero',
                'Toyota RAV4',
                'Honda CR-V',
            ],
            'Van' => [
                'Toyota Hiace',
                'Hyundai H1',
                'Mercedes V-Class',
                'Ford Transit',
            ],
            'Bus' => [
                'Toyota Coaster (30 Seater)',
                'King Long Bus (50 Seater)',
                'Yutong Bus (50 Seater)',
            ],
            'Luxury' => [
                'Mercedes S-Class',
                'BMW 7 Series',
                'Audi A8',
                'Lexus LS',
            ],
            'Electric' => [
                'Tesla Model S',
                'Tesla Model Y',
                'BYD Han',
                'BYD Atto 3',
            ],
            'Limousine' => [
                'Chrysler 300 Limousine',
                'Lincoln Town Car Limousine',
            ],
        ];

        foreach ($data as $typeName => $models) {
            $type = VehicleType::create([
                'name' => $typeName,
                'is_active' => true,
            ]);

            foreach ($models as $modelName) {
                VehicleModel::create([
                    'vehicle_type_id' => $type->id,
                    'name' => $modelName,
                    'is_active' => true,
                ]);
            }
        }
    }
}
