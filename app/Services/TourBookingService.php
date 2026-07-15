<?php

namespace App\Services;

use App\Models\TourBooking;
use App\Repositories\TourBookingRepository;

class TourBookingService
{
    public function __construct(protected TourBookingRepository $repository) {}

    public function list(?string $search = null)
    {
        return $this->repository->paginate($search);
    }

    public function find(TourBooking $booking): TourBooking
    {
        return $booking;
    }
}
