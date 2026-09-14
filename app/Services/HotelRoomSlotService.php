<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\HotelRoomSlot;
use App\Repositories\HotelRoomSlotRepository;

class HotelRoomSlotService
{
    public function __construct(
        protected HotelRoomSlotRepository $slotRepository,
        protected ActivityLogService $activityLog
    ) {}

    public function create(Hotel $hotel, array $data): HotelRoomSlot
    {
        if (empty($data['month'])) {
            $data['month'] = null;
            $data['season_type'] = null;
        }
        $payload = array_merge($data, ['hotel_id' => $hotel->id]);
        $slot = $this->slotRepository->create($payload);

        $this->activityLog->log('created', "created room slot \"{$slot->name}\" for hotel \"{$hotel->name}\"", $hotel);

        return $slot;
    }

    public function update(HotelRoomSlot $slot, array $data): HotelRoomSlot
    {
        if (empty($data['month'])) {
            $data['month'] = null;
            $data['season_type'] = null;
        }
        $slot = $this->slotRepository->update($slot, $data);

        $this->activityLog->log('updated', "updated room slot \"{$slot->name}\"", $slot->hotel);

        return $slot;
    }

    public function toggleStatus(HotelRoomSlot $slot): void
    {
        $slot->update(['is_active' => ! $slot->is_active]);

        $status = $slot->is_active ? 'activated' : 'deactivated';
        $this->activityLog->log('toggled_status', "{$status} room slot \"{$slot->name}\"", $slot->hotel);
    }

    public function delete(HotelRoomSlot $slot): void
    {
        $name = $slot->name;
        $hotel = $slot->hotel;
        $this->slotRepository->delete($slot);

        $this->activityLog->log('deleted', "deleted room slot \"{$name}\"", $hotel);
    }
}
