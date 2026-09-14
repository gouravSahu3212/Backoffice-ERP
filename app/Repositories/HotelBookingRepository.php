<?php

namespace App\Repositories;

use App\Models\HotelBooking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HotelBookingRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new HotelBooking;
    }

    public function paginateAll(?string $search = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return HotelBooking::query()
            ->with(['user', 'hotel.location', 'roomSlot'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('booking_reference', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhereHas('hotel', function ($hq) use ($search) {
                            $hq->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('roomSlot', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForAgent(int $userId, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return HotelBooking::query()
            ->with(['hotel.location', 'roomSlot'])
            ->where('user_id', $userId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('booking_reference', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhereHas('hotel', function ($hq) use ($search) {
                            $hq->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('roomSlot', function ($sq) use ($search) {
                            $sq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function updateStatus(HotelBooking $booking, string $status): HotelBooking
    {
        $booking->update(['status' => $status]);

        return $booking->fresh();
    }
}
