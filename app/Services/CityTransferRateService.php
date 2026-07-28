<?php

namespace App\Services;

use App\Models\CityTransferRate;
use App\Repositories\CityTransferRateRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class CityTransferRateService
{
    public function __construct(
        protected CityTransferRateRepository $repository,
        protected ActivityLogService $activityLog
    ) {}

    public function list(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->paginate($search, $perPage);
    }

    public function create(array $data): CityTransferRate
    {
        $rate = $this->repository->create($this->buildPayload($data));

        $this->activityLog->log('created', "created city transfer rate #{$rate->id}", $rate);

        return $rate;
    }

    public function update(CityTransferRate $rate, array $data): CityTransferRate
    {
        $rate = $this->repository->update($rate, $this->buildPayload($data));

        $this->activityLog->log('updated', "updated city transfer rate #{$rate->id}", $rate);

        return $rate;
    }

    public function toggleStatus(CityTransferRate $rate): void
    {
        $rate->update(['is_active' => ! $rate->is_active]);

        $status = $rate->is_active ? 'activated' : 'deactivated';
        $this->activityLog->log('toggled_status', "{$status} city transfer rate #{$rate->id}", $rate);
    }

    public function delete(CityTransferRate $rate): void
    {
        $rateId = $rate->id;
        $this->repository->delete($rate);

        $this->activityLog->log('deleted', "deleted city transfer rate #{$rateId}");
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
            'from_location_id' => $data['from_location_id'],
            'to_location_id' => $data['to_location_id'],
            'vehicle_type_id' => $data['vehicle_type_id'],
            'fare_type' => $data['fare_type'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'AED',
            'notes' => $data['notes'] ?? null,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
        ];
    }
}
