<?php

namespace App\Services;

use App\Models\FullDayTransferRate;
use App\Repositories\FullDayTransferRateRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class FullDayTransferRateService
{
    public function __construct(
        protected FullDayTransferRateRepository $repository
    ) {}

    public function list(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->paginate($search, $perPage);
    }

    public function create(array $data): FullDayTransferRate
    {
        return $this->repository->create($this->buildPayload($data));
    }

    public function update(FullDayTransferRate $rate, array $data): FullDayTransferRate
    {
        return $this->repository->update($rate, $this->buildPayload($data));
    }

    public function toggleStatus(FullDayTransferRate $rate): void
    {
        $rate->update(['is_active' => ! $rate->is_active]);
    }

    public function delete(FullDayTransferRate $rate): void
    {
        $this->repository->delete($rate);
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
            'vehicle_model_id' => $data['vehicle_model_id'] ?? null,
            'fare_type' => $data['fare_type'] ?? 'full_day',
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'AED',
            'notes' => $data['notes'] ?? null,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
        ];
    }
}
