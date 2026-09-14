<?php

namespace App\Repositories;

use App\Models\HotelRoomSlot;

class HotelRoomSlotRepository extends BaseRepository
{
    public function __construct(HotelRoomSlot $model)
    {
        $this->model = $model;
    }

    public function getByHotel(int $hotelId)
    {
        return $this->model->where('hotel_id', $hotelId)->orderBy('name')->get();
    }
}
