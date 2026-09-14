<?php

namespace App\Http\Controllers\Agent;

use App\Http\Requests\Agent\StoreHotelRequest;
use App\Http\Requests\Agent\UpdateHotelRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\HotelRoomSlot;
use App\Models\TransferLocation;
use App\Services\HotelBookingService;
use App\Services\HotelService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelController
{
    public function __construct(
        protected HotelService $hotelService,
        protected HotelBookingService $bookingService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $locationId = $request->filled('location_id') ? (int) $request->get('location_id') : null;
        $starRating = $request->filled('star_rating') ? (int) $request->get('star_rating') : null;
        $guests = $request->filled('guests') ? (int) $request->get('guests') : 2;
        $checkIn = $request->get('check_in', date('Y-m-d'));
        $checkOut = $request->get('check_out', date('Y-m-d', strtotime('+1 day')));

        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);
        $nights = max(1, $checkInDate->diffInDays($checkOutDate));

        $hotels = $this->hotelService->listAvailable($search, $locationId, $starRating, $guests, $checkIn, $checkOut, 15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'hotels' => $hotels,
            ]);
        }

        $locations = TransferLocation::active()->cities()->orderBy('name')->get();
        $amenities = Amenity::active()->orderBy('name')->get();

        return view('agent.hotels.index', compact(
            'hotels',
            'locations',
            'amenities',
            'search',
            'locationId',
            'starRating',
            'guests',
            'checkIn',
            'checkOut',
            'nights'
        ));
    }

    public function book(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hotel_room_slot_id' => ['required', 'exists:hotel_room_slots,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['nullable', 'integer', 'min:1'],
            'rooms_count' => ['nullable', 'integer', 'min:1'],
            'special_requests' => ['nullable', 'string'],
        ]);

        $slot = HotelRoomSlot::with('hotel')->findOrFail($validated['hotel_room_slot_id']);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = max(1, $checkIn->diffInDays($checkOut));
        $roomsCount = max(1, (int) ($validated['rooms_count'] ?? 1));

        $realAvailable = $this->hotelService->getSlotAvailableQty($slot->id, $validated['check_in'], $validated['check_out']);

        if ($roomsCount > $realAvailable) {
            return response()->json([
                'success' => false,
                'message' => $realAvailable > 0
                    ? "Only {$realAvailable} room(s) available for selected dates."
                    : 'No rooms available for the selected dates.',
            ], 422);
        }

        $totalPrice = $slot->price_per_night * $nights * $roomsCount;

        $booking = $this->bookingService->createBooking(auth()->user(), [
            'hotel_id' => $slot->hotel_id,
            'hotel_room_slot_id' => $slot->id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'guests' => $validated['guests'] ?? 1,
            'rooms_count' => $roomsCount,
            'price_per_night' => $slot->price_per_night,
            'total_price' => $totalPrice,
            'currency' => $slot->currency ?? 'AED',
            'special_requests' => $validated['special_requests'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Room booking request sent successfully! Reference: {$booking->booking_reference}",
            'booking' => $booking,
        ]);
    }

    public function store(StoreHotelRequest $request): JsonResponse
    {
        $hotel = $this->hotelService->create($request->validated(), auth()->user());
        $hotel->load('location');

        return response()->json([
            'success' => true,
            'message' => 'Hotel created successfully.',
            'hotel' => $this->formatHotel($hotel),
        ]);
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel): JsonResponse
    {
        $hotel = $this->hotelService->update($hotel, $request->validated());
        $hotel->load('location');

        return response()->json([
            'success' => true,
            'message' => 'Hotel updated successfully.',
            'hotel' => $this->formatHotel($hotel),
        ]);
    }

    public function toggleStatus(Hotel $hotel): JsonResponse
    {
        $this->hotelService->toggleStatus($hotel);
        $hotel->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Hotel status updated successfully.',
            'is_active' => $hotel->is_active,
        ]);
    }

    public function destroy(Hotel $hotel): JsonResponse
    {
        $this->hotelService->delete($hotel);

        return response()->json([
            'success' => true,
            'message' => 'Hotel deleted successfully.',
        ]);
    }

    public function storeLocation(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $location = $this->hotelService->createLocation($request->name, 'city');

        return response()->json([
            'success' => true,
            'message' => 'Location added successfully.',
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
            ],
        ]);
    }

    public function storeAmenity(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $amenity = $this->hotelService->createAmenity($request->name);

        return response()->json([
            'success' => true,
            'message' => 'Amenity added successfully.',
            'amenity' => [
                'id' => $amenity->id,
                'name' => $amenity->name,
            ],
        ]);
    }

    private function formatHotel(Hotel $hotel): array
    {
        return [
            'id' => $hotel->id,
            'name' => $hotel->name,
            'location_id' => $hotel->location_id,
            'location_name' => $hotel->location->name ?? 'N/A',
            'address' => $hotel->address ?? '',
            'description' => $hotel->description ?? '',
            'terms_and_conditions' => $hotel->terms_and_conditions ?? '',
            'star_rating' => (int) $hotel->star_rating,
            'amenities' => $hotel->amenities ?? [],
            'image_urls' => $hotel->image_urls ?? [],
            'is_active' => (bool) $hotel->is_active,
            'is_featured' => (bool) $hotel->is_featured,
        ];
    }
}
