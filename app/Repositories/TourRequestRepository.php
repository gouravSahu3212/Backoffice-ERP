<?php

namespace App\Repositories;

use App\Models\TourRequest;

class TourRequestRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new TourRequest();
    }

    public function paginateAll(?string $search = null, int $perPage = 20)
    {
        return TourRequest::with(['tour', 'agent'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('request_reference', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('passport_number', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForAgent(int $agentId, ?string $search = null, int $perPage = 20)
    {
        return TourRequest::with(['tour', 'agent'])
            ->where('agent_id', $agentId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('request_reference', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('passport_number', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }
}
