<?php

namespace App\Repositories;

use App\Models\AirportTransferZone;

class AirportTransferZoneRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new AirportTransferZone();
    }
}
