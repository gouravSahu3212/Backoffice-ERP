<?php

namespace App\Repositories;

use App\Models\Amenity;

class AmenityRepository extends BaseRepository
{
    public function __construct(Amenity $model)
    {
        $this->model = $model;
    }

    public function allActive()
    {
        return $this->model->active()->orderBy('name')->get();
    }
}
