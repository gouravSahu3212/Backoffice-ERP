<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\AgentRepository;

class AgentService
{
    public function __construct(
        protected AgentRepository $repository
    ) {}

    public function list(?string $search)
    {
        return $this->repository->paginate($search);
    }

    public function create(array $data)
    {
        $agent = $this->repository->create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'],
            'password'  => $data['password'],
            'is_active' => true,
        ]);

        $agent->assignRole('Agent');

        return $agent;
    }

    public function update(User $agent, array $data)
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $this->repository->update($agent, $data);
    }

    public function toggleStatus(User $agent)
    {
        $agent->update([
            'is_active' => !$agent->is_active
        ]);
    }
}