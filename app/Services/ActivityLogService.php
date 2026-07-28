<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Repositories\ActivityLogRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function __construct(
        protected ActivityLogRepository $repository
    ) {}

    /**
     * Record an activity log entry.
     */
    public function log(
        string $action,
        string $description,
        ?Model $subject = null,
        ?array $properties = null
    ): ActivityLog {
        $data = [
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if ($subject) {
            $data['subject_type'] = get_class($subject);
            $data['subject_id'] = $subject->getKey();
        }

        return $this->repository->create($data);
    }

    /**
     * Get paginated activity logs with filters.
     *
     * @param  array{search?: string, action?: string, date_from?: string, date_to?: string}  $filters
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $perPage);
    }

    /**
     * Get recent activity logs for the dashboard widget.
     */
    public function recent(int $limit = 10): Collection
    {
        return $this->repository->recent($limit);
    }

    /**
     * Get distinct action types for the filter dropdown.
     *
     * @return array<int, string>
     */
    public function distinctActions(): array
    {
        return $this->repository->distinctActions();
    }

    /**
     * Get summary stats for the activity log page.
     *
     * @return array{today: int, this_week: int}
     */
    public function stats(): array
    {
        return [
            'today' => $this->repository->countToday(),
            'this_week' => $this->repository->countThisWeek(),
        ];
    }
}
