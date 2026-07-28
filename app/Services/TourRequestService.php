<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\TourRequest;
use App\Models\User;
use App\Repositories\TourRequestRepository;
use Illuminate\Support\Str;

class TourRequestService
{
    public function __construct(
        protected TourRequestRepository $repository,
        protected ActivityLogService $activityLog
    ) {}

    /**
     * Create a new tour enquiry.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(Tour $tour, User $agent, array $data): TourRequest
    {
        $reference = 'TR-'.strtolower(Str::random(8));

        $tourRequest = $this->repository->create([
            'request_reference' => $reference,
            'tour_id' => $tour->id,
            'agent_id' => $agent->id,
            'customer_name' => $data['customer_name'],
            'date_of_birth' => $data['date_of_birth'],
            'passport_number' => $data['passport_number'],
            'departure_date' => $data['departure_date'],
            'pax' => (int) $data['pax'],
            'total_price' => (float) $data['total_price'],
            'currency' => $data['currency'] ?? 'USD',
            'status' => 'new',
        ]);

        $this->activityLog->log(
            'created',
            "submitted tour enquiry {$reference} for \"{$tour->title}\"",
            $tourRequest
        );

        return $tourRequest;
    }

    /**
     * List all enquiries (admin).
     */
    public function listAll(?string $search = null)
    {
        return $this->repository->paginateAll($search);
    }

    /**
     * List enquiries for a specific agent.
     */
    public function listForAgent(int $agentId, ?string $search = null)
    {
        return $this->repository->paginateForAgent($agentId, $search);
    }

    /**
     * Update the status of a tour request.
     */
    public function updateStatus(TourRequest $tourRequest, string $status): TourRequest
    {
        $tourRequest->update(['status' => $status]);

        $this->activityLog->log(
            'updated',
            "changed tour request {$tourRequest->request_reference} status to \"{$status}\"",
            $tourRequest
        );

        return $tourRequest->fresh();
    }
}
