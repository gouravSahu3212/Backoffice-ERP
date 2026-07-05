<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    protected Model $model;

    public function all()
    {
        return $this->model->all();
    }

    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data)
    {
        $model->update($data);

        return $model;
    }

    public function delete(Model $model)
    {
        return $model->delete();
    }
}