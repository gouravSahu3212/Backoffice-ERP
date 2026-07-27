<?php

namespace App\Http\Controllers\Admin;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends AdminController
{
    public function __construct(
        protected ActivityLogService $service
    ) {}

    /**
     * Display the activity log listing.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'action', 'date_from', 'date_to']);

        $logs = $this->service->list($filters);
        $actions = $this->service->distinctActions();
        $stats = $this->service->stats();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('admin.activity-logs._table', compact('logs'))->render(),
                'pagination' => $logs->links()->toHtml(),
                'stats' => $stats,
            ]);
        }

        return view('admin.activity-logs.index', compact('logs', 'actions', 'stats', 'filters'));
    }
}
