<?php

namespace App\Http\Controllers\Admin;

use App\Models\TourRequest;
use App\Services\TourRequestService;
use Illuminate\Http\Request;

class TourRequestController extends AdminController
{
    public function __construct(
        protected TourRequestService $service
    ) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $requests = $this->service->listAll($search);

        return view('admin.tours.requests.index', compact('requests', 'search'));
    }

    public function updateStatus(Request $request, TourRequest $tourRequest): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,confirmed,closed'],
        ]);

        $this->service->updateStatus($tourRequest, $request->status);

        return response()->json(['success' => true]);
    }
}
