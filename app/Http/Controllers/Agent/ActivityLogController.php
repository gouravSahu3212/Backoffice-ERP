<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function __construct(
        protected ActivityLogService $service
    ) {}

    /**
     * Display the activity log for the authenticated agent.
     */
    public function index(Request $request)
    {
        $agentId = auth()->id();
        $filters = $request->only(['search', 'action', 'date_from', 'date_to']);

        $logs = $this->service->listForAgent($agentId, $filters);
        $actions = $this->service->distinctActionsForAgent($agentId);

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('agent.activity-logs._table', compact('logs'))->render(),
                'pagination' => $logs->links()->toHtml(),
            ]);
        }

        return view('agent.activity-logs.index', compact('logs', 'actions', 'filters'));
    }
}
