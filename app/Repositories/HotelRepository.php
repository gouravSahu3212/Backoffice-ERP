<?php

namespace App\Repositories;

use App\Models\Hotel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HotelRepository extends BaseRepository
{
    public function __construct(Hotel $model)
    {
        $this->model = $model;
    }

    public function search(?string $search = null, ?int $locationId = null, ?int $starRating = null, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->with(['location', 'slots'])->latest();

        if ($search && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('address', 'like', $term);
            });
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        if ($starRating) {
            $query->where('star_rating', $starRating);
        }

        return $query->paginate($perPage);
    }
}
