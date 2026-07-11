<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreCityTransferRateRequest;
use App\Http\Requests\Admin\UpdateCityTransferRateRequest;
use App\Models\CityTransferRate;
use App\Models\TransferLocation;
use App\Models\VehicleType;
use App\Services\CityTransferRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityTransferRateController extends AdminController
{
    public function __construct(
        protected CityTransferRateService $service
    ) {}

    public function store(StoreCityTransferRateRequest $request): JsonResponse
    {
        $rate = $this->service->create($request->validated());
        $rate->load(['fromLocation', 'toLocation', 'vehicleType']);

        return response()->json([
            'success' => true,
            'message' => 'Rate created successfully.',
            'rate'    => $this->formatRate($rate),
            'row_html' => view('admin.transfers._city_rate_row', compact('rate'))->render(),
        ]);
    }

    public function update(UpdateCityTransferRateRequest $request, CityTransferRate $rate): JsonResponse
    {
        $rate = $this->service->update($rate, $request->validated());
        $rate->load(['fromLocation', 'toLocation', 'vehicleType']);

        return response()->json([
            'success' => true,
            'message' => 'Rate updated successfully.',
            'rate'    => $this->formatRate($rate),
            'row_html' => view('admin.transfers._city_rate_row', compact('rate'))->render(),
        ]);
    }

    public function toggleStatus(Request $request, CityTransferRate $rate): JsonResponse
    {
        $this->service->toggleStatus($rate);
        $rate->refresh();

        return response()->json([
            'success'   => true,
            'is_active' => $rate->is_active,
        ]);
    }

    public function destroy(CityTransferRate $rate): JsonResponse
    {
        $this->service->delete($rate);

        return response()->json([
            'success' => true,
            'message' => 'Rate deleted successfully.',
        ]);
    }

    /**
     * Endpoint to create a new location inline from the modal.
     */
    public function storeLocation(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:city,airport'],
        ]);

        $location = TransferLocation::create([
            'name'      => $request->name,
            'type'      => $request->type,
            'is_active' => true,
        ]);

        return response()->json([
            'success'  => true,
            'location' => ['id' => $location->id, 'name' => $location->name],
        ]);
    }

    /**
     * Endpoint to create a new vehicle type inline from the modal.
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
    private function formatRate(CityTransferRate $rate): array
    {
        return [
            'id'                => $rate->id,
            'from_location_id'  => $rate->from_location_id,
            'to_location_id'    => $rate->to_location_id,
            'vehicle_type_id'   => $rate->vehicle_type_id,
            'from_location'     => $rate->fromLocation->name,
            'to_location'       => $rate->toLocation->name,
            'vehicle_type'      => $rate->vehicleType->name,
            'fare_type'         => $rate->fare_type,
            'price'             => $rate->price,
            'currency'          => $rate->currency,
            'notes'             => $rate->notes,
            'is_active'         => $rate->is_active,
        ];
    }
}
