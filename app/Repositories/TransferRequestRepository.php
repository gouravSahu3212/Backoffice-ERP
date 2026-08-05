<?php

namespace App\Repositories;

use App\Models\TransferRequest;
use Illuminate\Pagination\LengthAwarePaginator;

class TransferRequestRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new TransferRequest;
    }

    public function paginateAll(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return TransferRequest::with(['agent'])
            ->when($search, function ($query) use ($search) {
                $query->where('request_reference', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('passport_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('route_label', 'like', "%{$search}%")
                    ->orWhereHas('agent', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateForAgent(int $agentId, ?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return TransferRequest::with(['agent'])
            ->where('agent_id', $agentId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('request_reference', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('passport_number', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('route_label', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
