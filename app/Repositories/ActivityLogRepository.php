<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ActivityLogRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ActivityLog;
    }

    /**
     * Paginate activity logs with optional filters.
     *
     * @param  array{search?: string, action?: string, date_from?: string, date_to?: string}  $filters
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return ActivityLog::with('user')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['action'] ?? null, function ($query, $action) {
                $query->where('action', $action);
            })
            ->when($filters['date_from'] ?? null, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get recent activity logs for the dashboard widget.
     */
    public function recent(int $limit = 10): Collection
    {
        return ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get distinct action types for the filter dropdown.
     *
     * @return array<int, string>
     */
    public function distinctActions(): array
    {
        return ActivityLog::distinct()
            ->pluck('action')
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Count activities created today.
     */
    public function countToday(): int
    {
        return ActivityLog::whereDate('created_at', today())->count();
    }

    /**
     * Count activities created this week.
     */
    public function countThisWeek(): int
    {
        return ActivityLog::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();
    }
}
