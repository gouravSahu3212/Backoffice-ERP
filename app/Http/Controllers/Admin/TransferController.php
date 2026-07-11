<?php

namespace App\Http\Controllers\Admin;

use App\Models\TransferLocation;
use App\Models\VehicleType;
use App\Repositories\CityTransferRateRepository;
use App\Services\CityTransferRateService;
use Illuminate\Http\Request;

class TransferController extends AdminController
{
    public function __construct(
        protected CityTransferRateService $cityRateService,
        protected CityTransferRateRepository $cityRateRepository
    ) {}

    public function index(Request $request)
    {
        $tab      = $request->get('tab', 'city');
        $search   = $request->search;
        $rates    = $this->cityRateService->list($search);
        $locations = TransferLocation::active()->orderBy('name')->get();
        $vehicleTypes = VehicleType::active()->orderBy('name')->get();

        return view('admin.transfers.index', compact('rates', 'locations', 'vehicleTypes', 'tab', 'search'));
    }
}
