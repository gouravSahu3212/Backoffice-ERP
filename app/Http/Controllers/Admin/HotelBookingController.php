<?php

namespace App\Http\Controllers\Admin;

use App\Models\HotelBooking;
use App\Services\HotelBookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelBookingController extends AdminController
{
    public function __construct(
        protected HotelBookingService $bookingService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $bookings = $this->bookingService->listAll($search, $status);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'bookings' => $bookings,
            ]);
        }

        return view('admin.hotels.bookings.index', compact('bookings', 'search', 'status'));
    }

    public function show(HotelBooking $hotelBooking): JsonResponse|View
    {
        $hotelBooking->load(['user', 'hotel.location', 'roomSlot']);

        $checkIn = Carbon::parse($hotelBooking->check_in);
        $checkOut = Carbon::parse($hotelBooking->check_out);
        $nights = max(1, $checkIn->diffInDays($checkOut));

        if (request()->expectsJson()) {
            $isPaid = $hotelBooking->status === 'confirmed';

            return response()->json([
                'success' => true,
                'booking' => [
                    'id' => $hotelBooking->id,
                    'booking_reference' => $hotelBooking->booking_reference,
                    'customer_name' => $hotelBooking->customer_name,
                    'customer_email' => $hotelBooking->customer_email,
                    'customer_phone' => $hotelBooking->customer_phone ?? 'N/A',
                    'hotel_name' => $hotelBooking->hotel->name ?? 'N/A',
                    'room_type' => $hotelBooking->roomSlot->room_type_name ?? 'Room',
                    'check_in' => $hotelBooking->check_in?->format('Y-m-d'),
                    'check_out' => $hotelBooking->check_out?->format('Y-m-d'),
                    'nights' => $nights,
                    'guests' => $hotelBooking->guests,
                    'rooms_count' => $hotelBooking->rooms_count,
                    'total_price' => number_format($hotelBooking->total_price, 2),
                    'currency' => $hotelBooking->currency,
                    'status' => $hotelBooking->status,
                    'special_requests' => $hotelBooking->special_requests ?? 'None',
                    'created_at' => $hotelBooking->created_at?->format('Y-m-d'),
                    'payment_status' => $isPaid ? 'paid' : '-',
                    'payment_method' => $isPaid ? 'card (paid)' : '-',
                ],
            ]);
        }

        return view('admin.hotels.bookings.show', [
            'booking' => $hotelBooking,
            'nights' => $nights,
        ]);
    }

    public function updateStatus(Request $request, HotelBooking $hotelBooking): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:new,confirmed,cancelled,rejected'],
        ]);

        $booking = $this->bookingService->updateStatus($hotelBooking, $request->status);

        return response()->json([
            'success' => true,
            'message' => "Booking {$booking->booking_reference} status updated to \"{$booking->status}\".",
            'status' => $booking->status,
        ]);
    }

    public function print(HotelBooking $hotelBooking): View
    {
        $hotelBooking->load(['user', 'hotel.location', 'roomSlot']);

        $checkIn = Carbon::parse($hotelBooking->check_in);
        $checkOut = Carbon::parse($hotelBooking->check_out);
        $nights = max(1, $checkIn->diffInDays($checkOut));

        return view('admin.hotels.bookings.print', [
            'booking' => $hotelBooking,
            'nights' => $nights,
        ]);
    }
}
