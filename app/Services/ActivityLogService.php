<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Repositories\ActivityLogRepository;
use Carbon\Carbon;
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
        ?array $properties = null,
        ?int $relatedAgentId = null
    ): ActivityLog {
        $data = [
            'user_id' => auth()->id(),
            'related_agent_id' => $relatedAgentId,
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

    /**
     * Get paginated activity logs scoped to a specific agent.
     *
     * @param  array{search?: string, action?: string, date_from?: string, date_to?: string}  $filters
     */
    public function listForAgent(int $agentId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateForAgent($agentId, $filters, $perPage);
    }

    /**
     * Get recent activity logs for a specific agent.
     */
    public function recentForAgent(int $agentId, int $limit = 10): Collection
    {
        return $this->repository->recentForAgent($agentId, $limit);
    }

    /**
     * Get distinct action types for logs related to a specific agent.
     *
     * @return array<int, string>
     */
    public function distinctActionsForAgent(int $agentId): array
    {
        return $this->repository->distinctActionsForAgent($agentId);
    }

    /**
     * Get the count of unseen activity logs for the given user.
     */
    public function getUnseenCount(User $user): int
    {
        $since = $user->activity_log_last_seen_at ?? Carbon::createFromTimestamp(0);

        if ($user->hasRole('Agent')) {
            return $this->repository->countNewSinceForAgent($user->id, $since);
        }

        return $this->repository->countNewSince($since);
    }

    /**
     * Mark activity logs as seen by updating the user's last-seen timestamp.
     */
    public function markAsSeen(User $user): void
    {
        $user->update(['activity_log_last_seen_at' => now()]);
    }
}
