<?php

namespace App\Http\Controllers\Agent;

use App\Models\HotelBooking;
use App\Services\HotelBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelBookingController
{
    public function __construct(
        protected HotelBookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $bookings = $this->bookingService->listForAgent(auth()->id(), $search);

        return view('agent.hotels.bookings.index', compact('bookings', 'search'));
    }

    public function cancel(HotelBooking $hotelBooking): JsonResponse
    {
        if ($hotelBooking->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        if ($hotelBooking->status === 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Confirmed bookings cannot be cancelled directly. Please contact admin to cancel confirmed bookings.',
            ], 422);
        }

        if (in_array($hotelBooking->status, ['cancelled', 'rejected'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Booking is already cancelled or rejected.',
            ], 422);
        }

        $booking = $this->bookingService->cancelBookingByAgent($hotelBooking, auth()->user());

        return response()->json([
            'success' => true,
            'message' => "Booking {$booking->booking_reference} has been cancelled successfully.",
            'booking' => $booking,
        ]);
    }
}
