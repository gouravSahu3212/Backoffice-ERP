<?php

namespace App\Http\Controllers\Admin;

use App\Models\AirportTransferZone;
use App\Models\TransferLocation;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Repositories\AirportTransferRateRepository;
use App\Repositories\CityTransferRateRepository;
use App\Services\AirportTransferRateService;
use App\Services\CityTransferRateService;
use App\Services\FullDayTransferRateService;
use Illuminate\Http\Request;

class TransferController extends AdminController
{
    public function __construct(
        protected CityTransferRateService $cityRateService,
        protected CityTransferRateRepository $cityRateRepository,
        protected AirportTransferRateService $airportRateService,
        protected AirportTransferRateRepository $airportRateRepository,
        protected FullDayTransferRateService $fullDayRateService
    ) {}

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'city');
        $search = $request->search;
        $rates = $this->cityRateService->list($search);
        $airportRates = $this->airportRateService->list($search);
        $fullDayRates = $this->fullDayRateService->list($search);
        $locations = TransferLocation::active()->orderBy('name')->get();
        $airports = TransferLocation::active()->airports()->orderBy('name')->get();
        $vehicleTypes = VehicleType::active()->orderBy('name')->get();
        $vehicleModels = VehicleModel::active()->orderBy('name')->get();
        $zones = AirportTransferZone::active()->orderBy('name')->get();

        return view('admin.transfers.index', compact(
            'rates',
            'airportRates',
            'fullDayRates',
            'locations',
            'airports',
            'vehicleTypes',
            'vehicleModels',
            'zones',
            'tab',
            'search'
        ));
    }
}
