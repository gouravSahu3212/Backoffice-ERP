<?php

namespace App\Repositories;

use App\Models\TransferLocation;
use Illuminate\Database\Eloquent\Collection;

class TransferLocationRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new TransferLocation();
    }

    /** @return Collection<int, TransferLocation> */
    public function allActive(): Collection
    {
        return TransferLocation::active()->orderBy('name')->get();
    }

    /** @return Collection<int, TransferLocation> */
    public function allCities(): Collection
    {
        return TransferLocation::active()->cities()->orderBy('name')->get();
    }

    /** @return Collection<int, TransferLocation> */
    public function allAirports(): Collection
    {
        return TransferLocation::active()->airports()->orderBy('name')->get();
    }
}
