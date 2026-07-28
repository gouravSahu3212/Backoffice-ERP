<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $actions = ['created', 'updated', 'deleted', 'toggled_status', 'logged_in', 'logged_out'];

        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement($actions),
            'description' => fake()->sentence(),
            'subject_type' => null,
            'subject_id' => null,
            'properties' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    /**
     * State: attach a polymorphic subject.
     */
    public function withSubject(string $subjectType, int $subjectId): static
    {
        return $this->state(fn () => [
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
        ]);
    }

    /**
     * State: a "created" action.
     */
    public function created(): static
    {
        return $this->state(fn () => [
            'action' => 'created',
        ]);
    }

    /**
     * State: a "deleted" action.
     */
    public function deleted(): static
    {
        return $this->state(fn () => [
            'action' => 'deleted',
        ]);
    }
}
