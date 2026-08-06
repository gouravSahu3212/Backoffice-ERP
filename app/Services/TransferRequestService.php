<?php

namespace App\Services;

use App\Models\TransferRequest;
use App\Models\User;
use App\Repositories\TransferRequestRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class TransferRequestService
{
    public function __construct(
        protected TransferRequestRepository $repository,
        protected ActivityLogService $activityLog
    ) {}

    /**
     * Create a new transfer enquiry.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(User $agent, array $data): TransferRequest
    {
        $reference = 'TRF-'.strtolower(Str::random(8));

        $transferRequest = $this->repository->create([
            'request_reference' => $reference,
            'agent_id' => $agent->id,
            'transfer_type_category' => $data['transfer_type_category'] ?? 'city',
            'city_rate_id' => $data['city_rate_id'] ?? null,
            'airport_rate_id' => $data['airport_rate_id'] ?? null,
            'full_day_rate_id' => $data['full_day_rate_id'] ?? null,
            'title' => $data['title'],
            'route_label' => $data['route_label'],
            'vehicle' => $data['vehicle'] ?? null,
            'customer_name' => $data['customer_name'],
            'date_of_birth' => $data['date_of_birth'],
            'passport_number' => $data['passport_number'],
            'pickup_date' => $data['pickup_date'] ?? null,
            'pickup_time' => $data['pickup_time'] ?? null,
            'total_price' => (float) $data['total_price'],
            'currency' => $data['currency'] ?? 'AED',
            'status' => 'new',
        ]);

        $this->activityLog->log(
            'created',
            "submitted transfer enquiry {$reference} for \"{$transferRequest->title}\"",
            $transferRequest,
            null,
            $agent->id
        );

        return $transferRequest;
    }

    /**
     * List all transfer enquiries (admin).
     */
    public function listAll(?string $search = null): LengthAwarePaginator
    {
        return $this->repository->paginateAll($search);
    }

    /**
     * List transfer enquiries for a specific agent.
     */
    public function listForAgent(int $agentId, ?string $search = null): LengthAwarePaginator
    {
        return $this->repository->paginateForAgent($agentId, $search);
    }

    /**
     * Update the status of a transfer request.
     */
    public function updateStatus(TransferRequest $transferRequest, string $status): TransferRequest
    {
        $transferRequest->update(['status' => $status]);

        $this->activityLog->log(
            'updated',
            "changed transfer request {$transferRequest->request_reference} status to \"{$status}\"",
            $transferRequest,
            null,
            $transferRequest->agent_id
        );

        return $transferRequest->fresh();
    }
}
