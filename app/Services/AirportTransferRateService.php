<?php

namespace App\Services;

use App\Models\AirportTransferRate;
use App\Repositories\AirportTransferRateRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AirportTransferRateService
{
    public function __construct(
        protected AirportTransferRateRepository $repository
    ) {}

    public function list(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return $this->repository->paginate($search, $perPage);
    }

    public function create(array $data): AirportTransferRate
    {
        return $this->repository->create($this->buildPayload($data));
    }

    public function update(AirportTransferRate $rate, array $data): AirportTransferRate
    {
        return $this->repository->update($rate, $this->buildPayload($data));
    }

    public function toggleStatus(AirportTransferRate $rate): void
    {
        $rate->update(['is_active' => ! $rate->is_active]);
    }

    public function delete(AirportTransferRate $rate): void
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
            'airport_id'      => $data['airport_id'],
            'transfer_type'   => $data['transfer_type'],
            'zone_id'         => $data['zone_id'],
            'vehicle_type_id' => $data['vehicle_type_id'],
            'fare_type'       => $data['fare_type'],
            'price'           => $data['price'],
            'currency'        => $data['currency'] ?? 'AED',
            'notes'           => $data['notes'] ?? null,
            'is_active'       => isset($data['is_active']) ? (bool) $data['is_active'] : true,
        ];
    }
}
