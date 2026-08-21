<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AgentAccountDeletedNotification;
use App\Notifications\AgentWelcomeNotification;
use App\Repositories\AgentRepository;
use Illuminate\Support\Facades\Password;

class AgentService
{
    public function __construct(
        protected AgentRepository $repository,
        protected ActivityLogService $activityLog
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

        $agent->notify(new AgentWelcomeNotification($resetUrl, $data['password']));

        $this->activityLog->log('created', "created agent \"{$agent->name}\"", $agent);

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

        $agent = $this->repository->update($agent, $payload);

        $this->activityLog->log('updated', "updated agent \"{$agent->name}\"", $agent);

        return $agent;
    }

    public function toggleStatus(User $agent): void
    {
        $agent->update(['is_active' => ! $agent->is_active]);

        $status = $agent->is_active ? 'activated' : 'deactivated';
        $this->activityLog->log('toggled_status', "{$status} agent \"{$agent->name}\"", $agent);
    }

    public function delete(User $agent): void
    {
        $name = $agent->name;

        $agent->notify(new AgentAccountDeletedNotification);

        $this->repository->delete($agent);

        $this->activityLog->log('deleted', "deleted agent \"{$name}\"");
    }
}
