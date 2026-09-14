<?php

namespace App\Services;

use App\Models\HotelBooking;
use App\Models\User;
use App\Notifications\HotelBookingNotification;
use App\Notifications\HotelBookingReceivedNotification;
use App\Notifications\HotelBookingStatusUpdatedNotification;
use App\Repositories\HotelBookingRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Notification;

class HotelBookingService
{
    public function __construct(
        protected HotelBookingRepository $repository,
        protected ActivityLogService $activityLog
    ) {}

    public function createBooking(User $user, array $data): HotelBooking
    {
        $nextId = (HotelBooking::max('id') ?? 0) + 1;
        $reference = 'BK-'.str_pad((string) $nextId, 3, '0', STR_PAD_LEFT);

        $booking = $this->repository->create([
            'booking_reference' => $reference,
            'user_id' => $user->id,
            'hotel_id' => $data['hotel_id'],
            'hotel_room_slot_id' => $data['hotel_room_slot_id'],
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'check_in' => $data['check_in'],
            'check_out' => $data['check_out'],
            'guests' => $data['guests'] ?? 1,
            'rooms_count' => $data['rooms_count'] ?? 1,
            'price_per_night' => $data['price_per_night'],
            'total_price' => $data['total_price'],
            'currency' => $data['currency'] ?? 'AED',
            'status' => 'new',
            'special_requests' => $data['special_requests'] ?? null,
        ]);

        $this->activityLog->log(
            'created',
            "submitted hotel room booking request {$reference} for \"".($booking->hotel->name ?? 'Hotel').'"',
            $booking,
            null,
            $user->id
        );

        $admins = User::role('Super Admin')->get();
        if ($admins->count() > 0) {
            Notification::send($admins, new HotelBookingReceivedNotification($booking));
        }

        if (! empty($booking->customer_email)) {
            Notification::route('mail', $booking->customer_email)
                ->notify(new HotelBookingNotification($booking, 'customer'));
        }

        return $booking;
    }

    public function listAll(?string $search = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateAll($search, $status, $perPage);
    }

    public function listForAgent(int $userId, ?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateForAgent($userId, $search, $perPage);
    }

    public function updateStatus(HotelBooking $booking, string $status): HotelBooking
    {
        $updatedBooking = $this->repository->updateStatus($booking, $status);

        $this->activityLog->log(
            'updated',
            "changed hotel booking {$updatedBooking->booking_reference} status to \"{$status}\"",
            $updatedBooking,
            null,
            $updatedBooking->user_id
        );

        if ($updatedBooking->user) {
            $updatedBooking->user->notify(new HotelBookingStatusUpdatedNotification($updatedBooking));
        }

        if (! empty($updatedBooking->customer_email)) {
            Notification::route('mail', $updatedBooking->customer_email)
                ->notify(new HotelBookingStatusUpdatedNotification($updatedBooking));
        }

        return $updatedBooking;
    }

    public function cancelBookingByAgent(HotelBooking $booking, User $agent): HotelBooking
    {
        $updatedBooking = $this->repository->updateStatus($booking, 'cancelled');

        $this->activityLog->log(
            'cancelled',
            "cancelled hotel booking {$updatedBooking->booking_reference}",
            $updatedBooking,
            null,
            $agent->id
        );

        $admins = User::role('Super Admin')->get();
        if ($admins->count() > 0) {
            Notification::send($admins, new HotelBookingStatusUpdatedNotification($updatedBooking));
        }

        if (! empty($updatedBooking->customer_email)) {
            Notification::route('mail', $updatedBooking->customer_email)
                ->notify(new HotelBookingStatusUpdatedNotification($updatedBooking));
        }

        return $updatedBooking;
    }
}
