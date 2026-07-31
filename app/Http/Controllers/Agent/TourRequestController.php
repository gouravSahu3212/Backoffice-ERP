<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\TourRequestService;
use Illuminate\Http\Request;

class TourRequestController extends Controller
{
    public function __construct(
        protected TourRequestService $service
    ) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $requests = $this->service->listForAgent(auth()->id(), $search);

        return view('agent.tours.requests.index', compact('requests', 'search'));
    }
}
