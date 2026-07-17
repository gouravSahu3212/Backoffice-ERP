<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AgentWelcomeNotification;
use App\Repositories\AgentRepository;
use Illuminate\Support\Facades\Password;

class AgentService
{
    public function __construct(
        protected AgentRepository $repository
    ) {}

    public function list(?string $search)
    {
        return $this->repository->paginate($search);
    }

    public function create(array $data): User
    {
        $agent = $this->repository->create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
        ]);

        $agent->assignRole('Agent');

        $token = Password::getRepository()->create($agent);
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $agent->email]);

        $agent->notify(new AgentWelcomeNotification($resetUrl));

        return $agent;
    }

    public function update(User $agent, array $data): User
    {
        $payload = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $agent->is_active,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        return $this->repository->update($agent, $payload);
    }

    public function toggleStatus(User $agent): void
    {
        $agent->update(['is_active' => ! $agent->is_active]);
    }

    public function delete(User $agent): void
    {
        $this->repository->delete($agent);
    }
}
