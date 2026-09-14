<?php

namespace App\Services;

use App\Repositories\HotelBookingRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HotelBookingService
{
    public function __construct(
        protected HotelBookingRepository $repository
    ) {}

    public function listForAgent(int $userId, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateForAgent($userId, $search, $perPage);
    }
}
