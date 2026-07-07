<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

class DashboardController
{
    public function __invoke()
    {
        $stats = [
            'agents' => User::role('Agent')->count(),
            'total_bookings' => 0,
            'hotels' => 0,
            'transfer_rates' => 0,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
