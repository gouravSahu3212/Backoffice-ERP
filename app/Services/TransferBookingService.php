<?php

namespace App\Services;

use App\Models\TransferBooking;
use App\Repositories\TransferBookingRepository;

class TransferBookingService
{
    public function __construct(protected TransferBookingRepository $repository) {}

    public function list(?string $search = null)
    {
        return $this->repository->paginate($search);
    }

    public function find(TransferBooking $booking): TransferBooking
    {
        return $booking;
    }
}
