<?php

namespace App\Services;

use App\Models\CityTransferRate;
use App\Repositories\CityTransferRateRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class CityTransferRateService
{
    public function __construct(
        protected CityTransferRateRepository $repository
    ) {}

    public function list(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->paginate($search, $perPage);
    }

    public function create(array $data): CityTransferRate
    {
        return $this->repository->create($this->buildPayload($data));
    }

    public function update(CityTransferRate $rate, array $data): CityTransferRate
    {
        return $this->repository->update($rate, $this->buildPayload($data));
    }

    public function toggleStatus(CityTransferRate $rate): void
    {
        $rate->update(['is_active' => ! $rate->is_active]);
    }

    public function delete(CityTransferRate $rate): void
    {
        $this->repository->delete($rate);
    }

    /**
     * Build a clean payload for create/update operations.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function buildPayload(array $data): array
    {
        return [
            'from_location_id' => $data['from_location_id'],
            'to_location_id'   => $data['to_location_id'],
            'vehicle_type_id'  => $data['vehicle_type_id'],
            'fare_type'        => $data['fare_type'],
            'price'            => $data['price'],
            'currency'         => $data['currency'] ?? 'AED',
            'notes'            => $data['notes'] ?? null,
            'is_active'        => isset($data['is_active']) ? (bool) $data['is_active'] : true,
        ];
    }
}
