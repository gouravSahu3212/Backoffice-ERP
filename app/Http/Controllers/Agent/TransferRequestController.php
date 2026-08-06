<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\TransferRequestService;
use Illuminate\Http\Request;

class TransferRequestController extends Controller
{
    public function __construct(
        protected TransferRequestService $service
    ) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $requests = $this->service->listForAgent(auth()->id(), $search);

        return view('agent.transfers.requests.index', compact('requests', 'search'));
    }
}
