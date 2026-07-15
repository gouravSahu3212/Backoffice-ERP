<?php

namespace App\Http\Controllers\Admin;

use App\Models\TransferBooking;
use App\Services\TransferBookingService;
use Illuminate\Http\Request;

class TransferBookingController extends AdminController
{
    public function __construct(protected TransferBookingService $service) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $bookings = $this->service->list($search);

        return view('admin.transfers.bookings.index', compact('bookings', 'search'));
    }

    public function show(TransferBooking $booking)
    {
        return view('admin.transfers.bookings.show', compact('booking'));
    }
}
