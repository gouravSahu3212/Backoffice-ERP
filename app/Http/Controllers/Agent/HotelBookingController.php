<?php

namespace App\Http\Controllers\Agent;

use App\Services\HotelBookingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelBookingController
{
    public function __construct(
        protected HotelBookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $bookings = $this->bookingService->listForAgent(auth()->id(), $search);

        return view('agent.hotels.bookings.index', compact('bookings', 'search'));
    }
}
