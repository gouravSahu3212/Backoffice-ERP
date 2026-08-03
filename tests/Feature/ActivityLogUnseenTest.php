<?php

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
});

it('returns zero unseen count when user has no last-seen timestamp and no logs exist', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $service = app(ActivityLogService::class);

    expect($service->getUnseenCount($admin))->toBe(0);
});

it('returns correct unseen count for admin after new activities are created', function () {
    $admin = User::factory()->create([
        'activity_log_last_seen_at' => now()->subHour(),
    ]);
    $admin->assignRole('Super Admin');

    // 2 old logs (before last seen)
    ActivityLog::factory()->count(2)->create([
        'user_id' => $admin->id,
        'created_at' => now()->subHours(2),
    ]);

    // 3 new logs (after last seen)
    ActivityLog::factory()->count(3)->create([
        'user_id' => $admin->id,
        'created_at' => now(),
    ]);

    $service = app(ActivityLogService::class);

    expect($service->getUnseenCount($admin))->toBe(3);
});

it('returns correct unseen count for agent scoped to their logs', function () {
    $agent = User::factory()->create([
        'activity_log_last_seen_at' => now()->subHour(),
    ]);
    $agent->assignRole('Agent');

    // 2 new logs for this agent
    ActivityLog::factory()->count(2)->forAgent($agent->id)->create([
        'created_at' => now(),
    ]);

    // 1 new log for a different agent (should not count)
    $otherAgent = User::factory()->create();
    ActivityLog::factory()->forAgent($otherAgent->id)->create([
        'created_at' => now(),
    ]);

    $service = app(ActivityLogService::class);

    expect($service->getUnseenCount($agent))->toBe(2);
});

it('marks activity log as seen and updates the user timestamp', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    expect($admin->activity_log_last_seen_at)->toBeNull();

    $service = app(ActivityLogService::class);
    $service->markAsSeen($admin);

    $admin->refresh();
    expect($admin->activity_log_last_seen_at)->not->toBeNull();
});

it('admin activity log page passes lastSeenAt to view and updates timestamp', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    ActivityLog::factory()->count(2)->create([
        'user_id' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.activity-logs.index'))
        ->assertOk()
        ->assertViewHas('lastSeenAt');

    // After visiting, the timestamp should be set
    $admin->refresh();
    expect($admin->activity_log_last_seen_at)->not->toBeNull();
});

it('agent activity log page passes lastSeenAt to view and updates timestamp', function () {
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    ActivityLog::factory()->count(2)->forAgent($agent->id)->create();

    $this->actingAs($agent)
        ->get(route('agent.activity-logs.index'))
        ->assertOk()
        ->assertViewHas('lastSeenAt');

    $agent->refresh();
    expect($agent->activity_log_last_seen_at)->not->toBeNull();
});

it('badge count resets to zero after visiting the activity log page', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    ActivityLog::factory()->count(5)->create([
        'user_id' => $admin->id,
    ]);

    $service = app(ActivityLogService::class);

    // Before visiting, unseen count should be 5
    expect($service->getUnseenCount($admin))->toBe(5);

    // Visit the page
    $this->actingAs($admin)
        ->get(route('admin.activity-logs.index'))
        ->assertOk();

    $admin->refresh();

    // After visiting, unseen count should be 0
    expect($service->getUnseenCount($admin))->toBe(0);
});
