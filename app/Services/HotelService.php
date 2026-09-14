<?php

namespace App\Services;

use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\TransferLocation;
use App\Models\User;
use App\Repositories\AmenityRepository;
use App\Repositories\HotelRepository;
use App\Repositories\TransferLocationRepository;
use Illuminate\Http\UploadedFile;

class HotelService
{
    public function __construct(
        protected HotelRepository $hotelRepository,
        protected AmenityRepository $amenityRepository,
        protected TransferLocationRepository $locationRepository,
        protected ActivityLogService $activityLog
    ) {}

    public function list(?string $search = null, ?int $locationId = null, ?int $starRating = null, int $perPage = 12)
    {
        return $this->hotelRepository->search($search, $locationId, $starRating, $perPage);
    }

    public function listAvailable(
        ?string $search = null,
        ?int $locationId = null,
        ?int $starRating = null,
        ?int $guests = null,
        ?string $checkIn = null,
        ?string $checkOut = null,
        int $perPage = 12
    ) {
        return $this->hotelRepository->searchAvailable($search, $locationId, $starRating, $guests, $checkIn, $checkOut, $perPage);
    }

    public function getSlotAvailableQty(int $slotId, string $checkIn, string $checkOut): int
    {
        return $this->hotelRepository->getSlotAvailableQty($slotId, $checkIn, $checkOut);
    }

    public function create(array $data, ?User $user = null): Hotel
    {
        $payload = $this->buildPayload($data);
        if ($user) {
            $payload['created_by'] = $user->id;
        }

        $hotel = $this->hotelRepository->create($payload);

        $this->activityLog->log('created', "created hotel \"{$hotel->name}\"", $hotel);

        return $hotel;
    }

    public function update(Hotel $hotel, array $data): Hotel
    {
        $hotel = $this->hotelRepository->update($hotel, $this->buildPayload($data, $hotel));

        $this->activityLog->log('updated', "updated hotel \"{$hotel->name}\"", $hotel);

        return $hotel;
    }

    public function toggleStatus(Hotel $hotel): void
    {
        $hotel->update(['is_active' => ! $hotel->is_active]);

        $status = $hotel->is_active ? 'activated' : 'deactivated';
        $this->activityLog->log('toggled_status', "{$status} hotel \"{$hotel->name}\"", $hotel);
    }

    public function delete(Hotel $hotel): void
    {
        $name = $hotel->name;
        $hotel->slots()->delete();
        $this->hotelRepository->delete($hotel);

        $this->activityLog->log('deleted', "deleted hotel \"{$name}\"");
    }

    public function createLocation(string $name, string $type = 'city'): TransferLocation
    {
        $location = TransferLocation::create([
            'name' => trim($name),
            'type' => $type,
            'is_active' => true,
        ]);

        $this->activityLog->log('created', "created location \"{$location->name}\"", $location);

        return $location;
    }

    public function createAmenity(string $name): Amenity
    {
        $amenity = Amenity::firstOrCreate(
            ['name' => trim($name)],
            ['is_active' => true]
        );

        $this->activityLog->log('created', "created amenity \"{$amenity->name}\"", $amenity);

        return $amenity;
    }

    private function buildPayload(array $data, ?Hotel $existing = null): array
    {
        $amenities = $data['amenities'] ?? [];
        if (is_string($amenities)) {
            $amenities = array_values(array_filter(array_map('trim', explode(',', $amenities))));
        }

        return [
            'name' => $data['name'],
            'location_id' => $data['location_id'] ?? null,
            'address' => $data['address'] ?? null,
            'description' => $data['description'] ?? null,
            'terms_and_conditions' => $data['terms_and_conditions'] ?? null,
            'star_rating' => isset($data['star_rating']) ? (int) $data['star_rating'] : 4,
            'amenities' => is_array($amenities) ? array_values(array_unique($amenities)) : [],
            'image_urls' => $this->normalizeImageUrls($data['image_urls'] ?? null, $existing?->image_urls ?? []),
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : ($existing?->is_active ?? true),
            'is_featured' => isset($data['is_featured']) ? (bool) $data['is_featured'] : ($existing?->is_featured ?? false),
        ];
    }

    /**
     * Store uploaded image files and keep existing values when no new files are provided.
     *
     * @param  array<int, string>  $existing
     * @return array<int, string>
     */
    private function normalizeImageUrls(mixed $value, array $existing = []): array
    {
        if ($value instanceof UploadedFile) {
            return [$value->store('hotels/images', 'public')];
        }

        if (is_array($value)) {
            $storedPaths = [];

            foreach ($value as $item) {
                if ($item instanceof UploadedFile) {
                    $storedPaths[] = $item->store('hotels/images', 'public');
                } elseif (is_string($item) && trim($item) !== '') {
                    $storedPaths[] = trim($item);
                }
            }

            if ($storedPaths !== []) {
                return $storedPaths;
            }
        }

        return $existing;
    }
}
