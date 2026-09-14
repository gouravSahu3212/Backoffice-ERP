<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Agent\StoreHotelRequest;
use App\Http\Requests\Agent\UpdateHotelRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\TransferLocation;
use App\Services\HotelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelController extends AdminController
{
    public function __construct(
        protected HotelService $hotelService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $search = $request->get('search');
        $locationId = $request->filled('location_id') ? (int) $request->get('location_id') : null;
        $starRating = $request->filled('star_rating') ? (int) $request->get('star_rating') : null;

        $hotels = $this->hotelService->list($search, $locationId, $starRating, 15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'hotels' => $hotels,
            ]);
        }

        $locations = TransferLocation::active()->cities()->orderBy('name')->get();
        $amenities = Amenity::active()->orderBy('name')->get();

        return view('admin.hotels.index', compact(
            'hotels',
            'locations',
            'amenities',
            'search',
            'locationId',
            'starRating'
        ));
    }

    public function store(StoreHotelRequest $request): JsonResponse
    {
        $hotel = $this->hotelService->create($request->validated(), auth()->user());
        $hotel->load(['location', 'slots']);

        return response()->json([
            'success' => true,
            'message' => 'Hotel created successfully.',
            'hotel' => $this->formatHotel($hotel),
        ]);
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel): JsonResponse
    {
        $hotel = $this->hotelService->update($hotel, $request->validated());
        $hotel->load(['location', 'slots']);

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
            'slots' => $hotel->relationLoaded('slots') ? $hotel->slots : [],
        ];
    }
}
