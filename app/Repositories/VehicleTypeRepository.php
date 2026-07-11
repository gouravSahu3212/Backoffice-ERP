<?php

namespace App\Repositories;

use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Collection;

class VehicleTypeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new VehicleType();
    }

    /** @return Collection<int, VehicleType> */
    public function allActive(): Collection
    {
        return VehicleType::active()->orderBy('name')->get();
    }
}
