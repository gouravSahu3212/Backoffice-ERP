<?php

namespace App\Http\Controllers\Admin;

use App\Models\AirportTransferZone;
use App\Models\TransferLocation;
use App\Models\VehicleType;
use App\Repositories\AirportTransferRateRepository;
use App\Repositories\CityTransferRateRepository;
use App\Services\AirportTransferRateService;
use App\Services\CityTransferRateService;
use Illuminate\Http\Request;

class TransferController extends AdminController
{
    public function __construct(
        protected CityTransferRateService $cityRateService,
        protected CityTransferRateRepository $cityRateRepository,
        protected AirportTransferRateService $airportRateService,
        protected AirportTransferRateRepository $airportRateRepository
    ) {}

    public function index(Request $request)
    {
        $tab      = $request->get('tab', 'city');
        $search   = $request->search;
        $rates    = $this->cityRateService->list($search);
        $airportRates = $this->airportRateService->list($search);
        $locations    = TransferLocation::active()->orderBy('name')->get();
        $airports     = TransferLocation::active()->airports()->orderBy('name')->get();
        $vehicleTypes = VehicleType::active()->orderBy('name')->get();
        $zones        = AirportTransferZone::active()->orderBy('name')->get();

        return view('admin.transfers.index', compact(
            'rates',
            'airportRates',
            'locations',
            'airports',
            'vehicleTypes',
            'zones',
            'tab',
            'search'
        ));
    }
}
