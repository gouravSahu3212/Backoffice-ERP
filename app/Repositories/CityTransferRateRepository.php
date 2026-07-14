<?php

namespace App\Repositories;

use App\Models\CityTransferRate;
use Illuminate\Pagination\LengthAwarePaginator;

class CityTransferRateRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new CityTransferRate();
    }

    public function paginate(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return CityTransferRate::with(['fromLocation', 'toLocation', 'vehicleType'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('fromLocation', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('toLocation', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('vehicleType', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
