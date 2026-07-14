<?php

namespace App\Repositories;

use App\Models\AirportTransferRate;
use Illuminate\Pagination\LengthAwarePaginator;

class AirportTransferRateRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new AirportTransferRate();
    }

    public function paginate(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return AirportTransferRate::with(['airport', 'zone', 'vehicleType'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('airport', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('zone', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('vehicleType', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
