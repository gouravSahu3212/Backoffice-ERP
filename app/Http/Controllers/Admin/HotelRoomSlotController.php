<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Agent\StoreHotelRoomSlotRequest;
use App\Http\Requests\Agent\UpdateHotelRoomSlotRequest;
use App\Models\Hotel;
use App\Models\HotelRoomSlot;
use App\Services\HotelRoomSlotService;
use Illuminate\Http\JsonResponse;

class HotelRoomSlotController extends AdminController
{
    public function __construct(
        protected HotelRoomSlotService $slotService
    ) {}

    public function store(StoreHotelRoomSlotRequest $request, Hotel $hotel): JsonResponse
    {
        $slot = $this->slotService->create($hotel, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Room slot added successfully.',
            'slot' => [
                'id' => $slot->id,
                'hotel_id' => $slot->hotel_id,
                'name' => $slot->name,
                'capacity' => (int) $slot->capacity,
                'available_qty' => (int) $slot->available_qty,
                'price_per_night' => (float) $slot->price_per_night,
                'currency' => $slot->currency,
                'month' => $slot->month,
                'season_type' => $slot->season_type,
                'is_active' => (bool) $slot->is_active,
            ],
        ]);
    }

    public function update(UpdateHotelRoomSlotRequest $request, HotelRoomSlot $slot): JsonResponse
    {
        $slot = $this->slotService->update($slot, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Room slot updated successfully.',
            'slot' => [
                'id' => $slot->id,
                'hotel_id' => $slot->hotel_id,
                'name' => $slot->name,
                'capacity' => (int) $slot->capacity,
                'available_qty' => (int) $slot->available_qty,
                'price_per_night' => (float) $slot->price_per_night,
                'currency' => $slot->currency,
                'month' => $slot->month,
                'season_type' => $slot->season_type,
                'is_active' => (bool) $slot->is_active,
            ],
        ]);
    }

    public function toggleStatus(HotelRoomSlot $slot): JsonResponse
    {
        $this->slotService->toggleStatus($slot);
        $slot->refresh();

        return response()->json([
            'success' => true,
            'message' => 'Room slot status updated.',
            'is_active' => $slot->is_active,
        ]);
    }

    public function destroy(HotelRoomSlot $slot): JsonResponse
    {
        $this->slotService->delete($slot);

        return response()->json([
            'success' => true,
            'message' => 'Room slot deleted successfully.',
        ]);
    }
}
