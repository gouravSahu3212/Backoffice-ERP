<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreAirportTransferRateRequest;
use App\Http\Requests\Admin\UpdateAirportTransferRateRequest;
use App\Models\AirportTransferRate;
use App\Models\AirportTransferZone;
use App\Models\VehicleType;
use App\Services\AirportTransferRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AirportTransferRateController extends AdminController
{
    public function __construct(
        protected AirportTransferRateService $service
    ) {}

    public function store(StoreAirportTransferRateRequest $request): JsonResponse
    {
        $rate = $this->service->create($request->validated());
        $rate->load(['airport', 'zone', 'vehicleType']);

        return response()->json([
            'success'  => true,
            'message'  => 'Rate created successfully.',
            'rate'     => $this->formatRate($rate),
            'row_html' => view('admin.transfers._airport_rate_row', compact('rate'))->render(),
        ]);
    }

    public function update(UpdateAirportTransferRateRequest $request, AirportTransferRate $rate): JsonResponse
    {
        $rate = $this->service->update($rate, $request->validated());
        $rate->load(['airport', 'zone', 'vehicleType']);

        return response()->json([
            'success'  => true,
            'message'  => 'Rate updated successfully.',
            'rate'     => $this->formatRate($rate),
            'row_html' => view('admin.transfers._airport_rate_row', compact('rate'))->render(),
        ]);
    }

    public function toggleStatus(Request $request, AirportTransferRate $rate): JsonResponse
    {
        $this->service->toggleStatus($rate);
        $rate->refresh();

        return response()->json([
            'success'   => true,
            'is_active' => $rate->is_active,
        ]);
    }

    public function destroy(AirportTransferRate $rate): JsonResponse
    {
        $this->service->delete($rate);

        return response()->json([
            'success' => true,
            'message' => 'Rate deleted successfully.',
        ]);
    }

    /**
     * Endpoint to create a new zone inline from the modal.
     */
    public function storeZone(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $zone = AirportTransferZone::create([
            'name'      => $request->name,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'zone'    => ['id' => $zone->id, 'name' => $zone->name],
        ]);
    }

    /**
     * Endpoint to create a new vehicle type inline from the airport modal.
     */
    public function storeVehicleType(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $vehicleType = VehicleType::create([
            'name'      => $request->name,
            'is_active' => true,
        ]);

        return response()->json([
            'success'      => true,
            'vehicle_type' => ['id' => $vehicleType->id, 'name' => $vehicleType->name],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatRate(AirportTransferRate $rate): array
    {
        return [
            'id'              => $rate->id,
            'airport_id'      => $rate->airport_id,
            'transfer_type'   => $rate->transfer_type,
            'zone_id'         => $rate->zone_id,
            'vehicle_type_id' => $rate->vehicle_type_id,
            'airport'         => $rate->airport->name,
            'zone'            => $rate->zone->name,
            'vehicle_type'    => $rate->vehicleType->name,
            'fare_type'       => $rate->fare_type,
            'price'           => $rate->price,
            'currency'        => $rate->currency,
            'notes'           => $rate->notes,
            'is_active'       => $rate->is_active,
        ];
    }
}
