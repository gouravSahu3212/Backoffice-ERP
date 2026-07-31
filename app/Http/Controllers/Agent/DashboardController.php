<?php

namespace App\Http\Controllers\Agent;

use App\Services\ActivityLogService;

class DashboardController
{
    public function __construct(
        protected ActivityLogService $activityLogService
    ) {}

    public function __invoke()
    {
        $recentActivities = $this->activityLogService->recentForAgent(auth()->id(), 10);

        return view('agent.dashboard', compact('recentActivities'));
    }
}
