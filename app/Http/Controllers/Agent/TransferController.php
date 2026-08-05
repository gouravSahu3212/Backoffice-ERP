<?php

namespace App\Http\Controllers\Agent;

use App\Http\Requests\Agent\StoreTransferRequestRequest;
use App\Models\AirportTransferRate;
use App\Models\AirportTransferZone;
use App\Models\CityTransferRate;
use App\Models\FullDayTransferRate;
use App\Models\TransferLocation;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Services\TransferRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TransferController
{
    public function __construct(
        protected TransferRequestService $transferRequestService
    ) {}

    public function index(Request $request): View
    {
        $tab = $request->get('tab', 'city');
        if (! in_array($tab, ['city', 'airport', 'fullday'], true)) {
            $tab = 'city';
        }

        $locations = TransferLocation::active()->orderBy('name')->get();
        $airports = TransferLocation::active()->airports()->orderBy('name')->get();
        $zones = AirportTransferZone::active()->orderBy('name')->get();
        $vehicleTypes = VehicleType::active()->orderBy('name')->get();
        $vehicleModels = VehicleModel::active()->orderBy('name')->get();

        $rates = $this->getRatesByTab($tab, $request);

        return view('agent.transfers.index', compact(
            'tab',
            'locations',
            'airports',
            'zones',
            'vehicleTypes',
            'vehicleModels',
            'rates'
        ));
    }

    public function search(Request $request): JsonResponse
    {
        $tab = $request->get('tab', 'city');
        if (! in_array($tab, ['city', 'airport', 'fullday'], true)) {
            $tab = 'city';
        }

        $rates = $this->getRatesByTab($tab, $request);

        return response()->json([
            'success' => true,
            'tab' => $tab,
            'count' => $rates->count(),
            'transfers' => $rates->values(),
        ]);
    }

    public function enquire(StoreTransferRequestRequest $request): JsonResponse
    {
        $transferRequest = $this->transferRequestService->create(
            auth()->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Transfer booking enquiry submitted successfully.',
            'reference' => $transferRequest->request_reference,
        ]);
    }

    private function getRatesByTab(string $tab, Request $request): Collection
    {
        if ($tab === 'airport') {
            return $this->searchAirportRates($request);
        }

        if ($tab === 'fullday') {
            return $this->searchFullDayRates($request);
        }

        return $this->searchCityRates($request);
    }

    private function searchCityRates(Request $request): Collection
    {
        $query = CityTransferRate::with(['fromLocation', 'toLocation', 'vehicleType'])
            ->where('is_active', true)
            ->latest();

        if ($request->filled('from_location_id')) {
            $query->where('from_location_id', $request->from_location_id);
        }

        if ($request->filled('to_location_id')) {
            $query->where('to_location_id', $request->to_location_id);
        }

        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        if ($request->filled('fare_type')) {
            $query->where('fare_type', $request->fare_type);
        }

        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        return $query->get()->map(fn (CityTransferRate $rate) => [
            'id' => $rate->id,
            'tab' => 'city',
            'title' => 'Economy '.($rate->vehicleType->name ?? 'Sedan'),
            'badge' => $rate->vehicleType->name ?? 'Sedan',
            'from_location' => $rate->fromLocation->name ?? 'N/A',
            'to_location' => $rate->toLocation->name ?? 'N/A',
            'route_label' => ($rate->fromLocation->name ?? 'N/A').' → '.($rate->toLocation->name ?? 'N/A'),
            'vehicle' => $rate->vehicleType->name ?? 'Standard Vehicle',
            'pax' => $this->guessPaxCount($rate->vehicleType->name ?? ''),
            'luggage' => $this->guessLuggageCount($rate->vehicleType->name ?? ''),
            'duration' => '~1.5h',
            'fare_type' => $rate->fare_type ?? 'fixed',
            'price' => (float) $rate->price,
            'currency' => $rate->currency ?? 'AED',
            'notes' => $rate->notes ?? 'Comfortable sedan for city rides',
        ]);
    }

    private function searchAirportRates(Request $request): Collection
    {
        $query = AirportTransferRate::with(['airport', 'zone', 'vehicleType'])
            ->where('is_active', true)
            ->latest();

        if ($request->filled('airport_id')) {
            $query->where('airport_id', $request->airport_id);
        }

        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        if ($request->filled('transfer_type')) {
            $query->where('transfer_type', $request->transfer_type);
        }

        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        if ($request->filled('fare_type')) {
            $query->where('fare_type', $request->fare_type);
        }

        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        return $query->get()->map(fn (AirportTransferRate $rate) => [
            'id' => $rate->id,
            'tab' => 'airport',
            'title' => 'Airport Express '.($rate->vehicleType->name ?? 'Transfer'),
            'badge' => $rate->vehicleType->name ?? 'Sedan',
            'airport' => $rate->airport->name ?? 'Airport',
            'zone' => $rate->zone->name ?? 'Zone',
            'route_label' => ($rate->airport->name ?? 'Airport').' ↔ '.($rate->zone->name ?? 'Zone'),
            'vehicle' => $rate->vehicleType->name ?? 'Standard Vehicle',
            'transfer_type' => ucfirst($rate->transfer_type ?? 'pickup'),
            'pax' => $this->guessPaxCount($rate->vehicleType->name ?? ''),
            'luggage' => $this->guessLuggageCount($rate->vehicleType->name ?? ''),
            'duration' => '~45m',
            'fare_type' => $rate->fare_type ?? 'fixed',
            'price' => (float) $rate->price,
            'currency' => $rate->currency ?? 'AED',
            'notes' => $rate->notes ?? 'Direct airport transfer with luggage assistance',
        ]);
    }

    private function searchFullDayRates(Request $request): Collection
    {
        $query = FullDayTransferRate::with(['fromLocation', 'toLocation', 'vehicleType', 'vehicleModel'])
            ->where('is_active', true)
            ->latest();

        if ($request->filled('from_location_id')) {
            $query->where('from_location_id', $request->from_location_id);
        }

        if ($request->filled('to_location_id')) {
            $query->where('to_location_id', $request->to_location_id);
        }

        if ($request->filled('vehicle_type_id')) {
            $query->where('vehicle_type_id', $request->vehicle_type_id);
        }

        if ($request->filled('fare_type')) {
            $query->where('fare_type', $request->fare_type);
        }

        if ($request->filled('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        return $query->get()->map(fn (FullDayTransferRate $rate) => [
            'id' => $rate->id,
            'tab' => 'fullday',
            'title' => ($rate->vehicleModel->name ?? $rate->vehicleType->name ?? 'Full-day Chauffeur'),
            'badge' => $rate->vehicleType->name ?? 'SUV',
            'from_location' => $rate->fromLocation->name ?? 'N/A',
            'to_location' => $rate->toLocation->name ?? 'N/A',
            'route_label' => ($rate->fromLocation->name ?? 'N/A').' → '.($rate->toLocation->name ?? 'N/A'),
            'vehicle' => $rate->vehicleModel->name ?? $rate->vehicleType->name ?? 'Chauffeur Vehicle',
            'pax' => $this->guessPaxCount($rate->vehicleType->name ?? ''),
            'luggage' => $this->guessLuggageCount($rate->vehicleType->name ?? ''),
            'duration' => $rate->fare_type === 'half_day' ? 'Half Day (~5h)' : 'Full Day (~10h)',
            'fare_type' => $rate->fare_type ?? 'full_day',
            'price' => (float) $rate->price,
            'currency' => $rate->currency ?? 'AED',
            'notes' => $rate->notes ?? 'Private vehicle with dedicated driver for full-day service',
        ]);
    }

    private function guessPaxCount(string $vehicleType): int
    {
        $type = strtolower($vehicleType);
        if (str_contains($type, 'suv')) {
            return 5;
        }
        if (str_contains($type, 'van') || str_contains($type, 'bus')) {
            return 8;
        }
        if (str_contains($type, 'luxury')) {
            return 3;
        }

        return 3;
    }

    private function guessLuggageCount(string $vehicleType): int
    {
        $type = strtolower($vehicleType);
        if (str_contains($type, 'suv')) {
            return 4;
        }
        if (str_contains($type, 'van') || str_contains($type, 'bus')) {
            return 6;
        }

        return 2;
    }
}
