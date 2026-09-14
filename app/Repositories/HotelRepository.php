<?php

namespace App\Repositories;

use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\HotelRoomSlot;
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

    public function searchAvailable(
        ?string $search = null,
        ?int $locationId = null,
        ?int $starRating = null,
        ?int $guests = null,
        ?string $checkIn = null,
        ?string $checkOut = null,
        ?string $month = null,
        ?string $seasonType = null,
        int $perPage = 12
    ): LengthAwarePaginator {
        $cIn = $checkIn ? date('Y-m-d', strtotime($checkIn)) : date('Y-m-d');
        $cOut = $checkOut ? date('Y-m-d', strtotime($checkOut)) : date('Y-m-d', strtotime('+1 day'));

        $query = $this->model->newQuery()
            ->active()
            ->with(['location', 'slots' => function ($q) use ($guests, $month, $seasonType) {
                $q->active()->where('available_qty', '>', 0);
                if ($guests && $guests > 0) {
                    $q->where('capacity', '>=', $guests);
                }
                if ($month && trim($month) !== '') {
                    $q->where('month', $month);
                }
                if ($seasonType && trim($seasonType) !== '') {
                    $q->where('season_type', $seasonType);
                }
            }])
            ->whereHas('slots', function ($q) use ($guests, $month, $seasonType) {
                $q->active()->where('available_qty', '>', 0);
                if ($guests && $guests > 0) {
                    $q->where('capacity', '>=', $guests);
                }
                if ($month && trim($month) !== '') {
                    $q->where('month', $month);
                }
                if ($seasonType && trim($seasonType) !== '') {
                    $q->where('season_type', $seasonType);
                }
            });

        if ($search && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhereHas('location', function ($lq) use ($term) {
                        $lq->where('name', 'like', $term);
                    });
            });
        }

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        if ($starRating) {
            $query->where('star_rating', '>=', $starRating);
        }

        $hotels = $query->latest()->paginate($perPage);

        // Deduct overlapping bookings for requested check-in and check-out dates
        $hotels->getCollection()->transform(function (Hotel $hotel) use ($cIn, $cOut, $guests, $month, $seasonType) {
            $validSlots = $hotel->slots->filter(function ($slot) use ($cIn, $cOut, $guests, $month, $seasonType) {
                if (! $slot->is_active || ($guests && $slot->capacity < $guests)) {
                    return false;
                }

                if ($month && trim($month) !== '' && $slot->month !== $month) {
                    return false;
                }

                if ($seasonType && trim($seasonType) !== '' && $slot->season_type !== $seasonType) {
                    return false;
                }

                $bookedRooms = HotelBooking::where('hotel_room_slot_id', $slot->id)
                    ->whereNotIn('status', ['cancelled', 'rejected'])
                    ->where('check_in', '<', $cOut)
                    ->where('check_out', '>', $cIn)
                    ->sum('rooms_count');

                $realAvailable = max(0, $slot->available_qty - (int) $bookedRooms);
                $slot->real_available_qty = $realAvailable;

                return $realAvailable > 0;
            });

            $hotel->setRelation('slots', $validSlots->values());

            return $hotel;
        });

        // Filter collection to only keep hotels that have at least 1 available room for the date range
        $filteredCollection = $hotels->getCollection()->filter(function (Hotel $hotel) {
            return $hotel->slots->count() > 0;
        })->values();

        $hotels->setCollection($filteredCollection);

        return $hotels;
    }

    public function getSlotAvailableQty(int $slotId, string $checkIn, string $checkOut): int
    {
        $slot = HotelRoomSlot::findOrFail($slotId);
        $cIn = date('Y-m-d', strtotime($checkIn));
        $cOut = date('Y-m-d', strtotime($checkOut));

        $bookedRooms = HotelBooking::where('hotel_room_slot_id', $slotId)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->where('check_in', '<', $cOut)
            ->where('check_out', '>', $cIn)
            ->sum('rooms_count');

        return max(0, $slot->available_qty - (int) $bookedRooms);
    }
}
