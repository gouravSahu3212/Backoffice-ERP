<?php

namespace App\Http\Controllers\Admin;

use App\Models\TourBooking;
use App\Services\TourBookingService;
use Illuminate\Http\Request;

class TourBookingController extends AdminController
{
    public function __construct(protected TourBookingService $service) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $bookings = $this->service->list($search);

        return view('admin.tours.bookings.index', compact('bookings', 'search'));
    }

    public function show(TourBooking $booking)
    {
        return view('admin.tours.bookings.show', compact('booking'));
    }
}

