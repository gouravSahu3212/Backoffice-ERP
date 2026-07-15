<?php

namespace App\Repositories;

use App\Models\Tour;

class TourRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Tour();
    }

    public function paginate(?string $search = null, int $perPage = 12)
    {
        return Tour::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateRequests(?string $search = null, int $perPage = 12)
    {
        return Tour::query()
            // ->where('status', 'pending')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
