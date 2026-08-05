<?php

namespace App\Http\Controllers\Admin;

use App\Models\TransferRequest;
use App\Services\TransferRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferRequestController extends AdminController
{
    public function __construct(
        protected TransferRequestService $service
    ) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $requests = $this->service->listAll($search);

        return view('admin.transfers.requests.index', compact('requests', 'search'));
    }

    public function updateStatus(Request $request, TransferRequest $transferRequest): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,confirmed,closed'],
        ]);

        $this->service->updateStatus($transferRequest, $request->status);

        return response()->json(['success' => true]);
    }
}
