<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\ActivityLogService;

class DashboardController
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function __invoke()
    {
        $stats = [
            'agents' => User::role('Agent')->count(),
            'total_bookings' => 0,
            'hotels' => 0,
            'transfer_rates' => 0,
        ];

        $recentActivities = $this->activityLogService->recent(10);

        return view('admin.dashboard', compact('stats', 'recentActivities'));
    }
}
