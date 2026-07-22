<?php

namespace App\Repositories;

use App\Models\FullDayTransferRate;
use Illuminate\Pagination\LengthAwarePaginator;

class FullDayTransferRateRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new FullDayTransferRate;
    }

    public function paginate(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return FullDayTransferRate::with(['fromLocation', 'toLocation', 'vehicleType', 'vehicleModel'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('fromLocation', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('toLocation', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('vehicleType', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('vehicleModel', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
