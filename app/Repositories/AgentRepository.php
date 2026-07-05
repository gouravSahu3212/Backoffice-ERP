<?php

namespace App\Repositories;

use App\Models\User;

class AgentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new User();
    }

    public function paginate(?string $search = null, int $perPage = 10)
    {
        return User::role('Agent')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }
}