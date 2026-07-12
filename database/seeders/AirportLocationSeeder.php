<?php

namespace Database\Seeders;

use App\Models\TransferLocation;
use Illuminate\Database\Seeder;

class AirportLocationSeeder extends Seeder
{
    public function run(): void
    {
        $airports = [
            'Dubai International (DXB)',
            'Abu Dhabi International (AUH)',
            'Sharjah International (SHJ)',
        ];

        foreach ($airports as $name) {
            TransferLocation::firstOrCreate(
                ['name' => $name, 'type' => 'airport'],
                ['is_active' => true]
            );
        }
    }
}
