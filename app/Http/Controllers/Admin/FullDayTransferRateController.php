<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreFullDayTransferRateRequest;
use App\Http\Requests\Admin\UpdateFullDayTransferRateRequest;
use App\Models\FullDayTransferRate;
use App\Models\VehicleModel;
use App\Services\FullDayTransferRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FullDayTransferRateController extends AdminController
{
    public function __construct(
        protected FullDayTransferRateService $service
    ) {}

    public function store(StoreFullDayTransferRateRequest $request): JsonResponse
    {
        $rate = $this->service->create($request->validated());
        $rate->load(['fromLocation', 'toLocation', 'vehicleType', 'vehicleModel']);

        return response()->json([
            'success' => true,
            'message' => 'Full-day rate created successfully.',
            'rate' => $this->formatRate($rate),
            'row_html' => view('admin.transfers._full_day_rate_row', compact('rate'))->render(),
        ]);
    }

    public function update(UpdateFullDayTransferRateRequest $request, FullDayTransferRate $rate): JsonResponse
    {
        $rate = $this->service->update($rate, $request->validated());
        $rate->load(['fromLocation', 'toLocation', 'vehicleType', 'vehicleModel']);

        return response()->json([
            'success' => true,
            'message' => 'Full-day rate updated successfully.',
            'rate' => $this->formatRate($rate),
            'row_html' => view('admin.transfers._full_day_rate_row', compact('rate'))->render(),
        ]);
    }

    public function toggleStatus(FullDayTransferRate $rate): JsonResponse
    {
        $this->service->toggleStatus($rate);
        $rate->refresh();

        return response()->json([
            'success' => true,
            'is_active' => $rate->is_active,
        ]);
    }

    public function destroy(FullDayTransferRate $rate): JsonResponse
    {
        $this->service->delete($rate);

        return response()->json([
            'success' => true,
            'message' => 'Full-day rate deleted successfully.',
        ]);
    }

    public function storeVehicleModel(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_type_id' => ['required', 'integer', 'exists:vehicle_types,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $vehicleModel = VehicleModel::create([
            'vehicle_type_id' => $request->vehicle_type_id,
            'name' => $request->name,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'vehicle_model' => [
                'id' => $vehicleModel->id,
                'name' => $vehicleModel->name,
                'vehicle_type_id' => $vehicleModel->vehicle_type_id,
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatRate(FullDayTransferRate $rate): array
    {
        return [
            'id' => $rate->id,
            'from_location_id' => $rate->from_location_id,
            'to_location_id' => $rate->to_location_id,
            'vehicle_type_id' => $rate->vehicle_type_id,
            'vehicle_model_id' => $rate->vehicle_model_id,
            'from_location' => $rate->fromLocation->name,
            'to_location' => $rate->toLocation->name,
            'vehicle_type' => $rate->vehicleType->name,
            'vehicle_model' => $rate->vehicleModel?->name ?? 'All Models',
            'fare_type' => $rate->fare_type,
            'price' => $rate->price,
            'currency' => $rate->currency,
            'notes' => $rate->notes,
            'is_active' => $rate->is_active,
        ];
    }
}
