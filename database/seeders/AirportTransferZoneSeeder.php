<?php

namespace Database\Seeders;

use App\Models\AirportTransferZone;
use Illuminate\Database\Seeder;

class AirportTransferZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            'City Center',
            'Marina',
            'JBR',
            'Downtown',
            'Business Bay',
            'Deira',
            'Jumeirah',
        ];

        foreach ($zones as $name) {
            AirportTransferZone::firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
