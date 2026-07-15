<?php

namespace App\Repositories;

use App\Models\TourBooking;

class TourBookingRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new TourBooking;
    }

    public function paginate(?string $search = null, int $perPage = 12)
    {
        return TourBooking::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('booking_reference', 'like', "%{$search}%")
                        ->orWhere('guest_name', 'like', "%{$search}%")
                        ->orWhere('guest_email', 'like', "%{$search}%")
                        ->orWhere('pickup_location', 'like', "%{$search}%")
                        ->orWhere('dropoff_location', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
