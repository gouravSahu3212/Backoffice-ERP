<?php

namespace App\Services;

use App\Models\AirportTransferRate;
use App\Repositories\AirportTransferRateRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AirportTransferRateService
{
    public function __construct(
        protected AirportTransferRateRepository $repository,
        protected ActivityLogService $activityLog
    ) {}

    public function list(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->paginate($search, $perPage);
    }

    public function create(array $data): AirportTransferRate
    {
        $rate = $this->repository->create($this->buildPayload($data));

        $this->activityLog->log('created', "created airport transfer rate #{$rate->id}", $rate);

        return $rate;
    }

    public function update(AirportTransferRate $rate, array $data): AirportTransferRate
    {
        $rate = $this->repository->update($rate, $this->buildPayload($data));

        $this->activityLog->log('updated', "updated airport transfer rate #{$rate->id}", $rate);

        return $rate;
    }

    public function toggleStatus(AirportTransferRate $rate): void
    {
        $rate->update(['is_active' => ! $rate->is_active]);

        $status = $rate->is_active ? 'activated' : 'deactivated';
        $this->activityLog->log('toggled_status', "{$status} airport transfer rate #{$rate->id}", $rate);
    }

    public function delete(AirportTransferRate $rate): void
    {
        $rateId = $rate->id;
        $this->repository->delete($rate);

        $this->activityLog->log('deleted', "deleted airport transfer rate #{$rateId}");
    }

    /**
     * Build a clean payload for create/update operations.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function buildPayload(array $data): array
    {
        return [
            'airport_id' => $data['airport_id'],
            'transfer_type' => $data['transfer_type'],
            'zone_id' => $data['zone_id'],
            'vehicle_type_id' => $data['vehicle_type_id'],
            'fare_type' => $data['fare_type'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'AED',
            'notes' => $data['notes'] ?? null,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
        ];
    }
}
