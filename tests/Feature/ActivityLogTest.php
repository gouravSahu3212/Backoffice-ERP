<?php

use App\Models\ActivityLog;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');
});

it('admin can view the activity log index page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.activity-logs.index'))
        ->assertOk()
        ->assertViewIs('admin.activity-logs.index');
});

it('activity log page displays logged activities', function () {
    ActivityLog::factory()->count(3)->create([
        'user_id' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.activity-logs.index'))
        ->assertOk()
        ->assertViewHas('logs', fn ($logs) => $logs->count() === 3);
});

it('activity log can be filtered by action type', function () {
    ActivityLog::factory()->create([
        'user_id' => $this->admin->id,
        'action' => 'created',
        'description' => 'Created something',
    ]);

    ActivityLog::factory()->create([
        'user_id' => $this->admin->id,
        'action' => 'deleted',
        'description' => 'Deleted something',
    ]);

    $this->actingAs($this->admin)
        ->getJson(route('admin.activity-logs.index', ['action' => 'created']))
        ->assertOk()
        ->assertJsonStructure(['html', 'pagination', 'stats']);
});

it('activity log can be searched by description', function () {
    ActivityLog::factory()->create([
        'user_id' => $this->admin->id,
        'description' => 'Created agent John Doe',
    ]);

    ActivityLog::factory()->create([
        'user_id' => $this->admin->id,
        'description' => 'Deleted tour Paris Trip',
    ]);

    $this->actingAs($this->admin)
        ->getJson(route('admin.activity-logs.index', ['search' => 'John']))
        ->assertOk();
});

it('non-admin users cannot access activity log page', function () {
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    $this->actingAs($agent)
        ->get(route('admin.activity-logs.index'))
        ->assertForbidden();
});

it('activity log stats are calculated correctly', function () {
    ActivityLog::factory()->count(5)->create([
        'user_id' => $this->admin->id,
        'created_at' => now(),
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.activity-logs.index'))
        ->assertOk()
        ->assertViewHas('stats', fn ($stats) => $stats['today'] === 5 && $stats['this_week'] === 5);
});

it('dashboard shows recent activities', function () {
    ActivityLog::factory()->count(3)->create([
        'user_id' => $this->admin->id,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('recentActivities');
});

// ── Agent Activity Log Tests ─────────────────────────────────────────────

it('agent can view their activity log page', function () {
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    $this->actingAs($agent)
        ->get(route('agent.activity-logs.index'))
        ->assertOk()
        ->assertViewIs('agent.activity-logs.index');
});

it('agent only sees activities related to them', function () {
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    $otherAgent = User::factory()->create();
    $otherAgent->assignRole('Agent');

    // Logs for this agent
    ActivityLog::factory()->count(2)->forAgent($agent->id)->create([
        'user_id' => $this->admin->id,
        'description' => 'Changed your tour request status',
    ]);

    // Logs for another agent (should not appear)
    ActivityLog::factory()->count(3)->forAgent($otherAgent->id)->create([
        'user_id' => $this->admin->id,
    ]);

    $this->actingAs($agent)
        ->get(route('agent.activity-logs.index'))
        ->assertOk()
        ->assertViewHas('logs', fn ($logs) => $logs->count() === 2);
});

it('agent activity log can be filtered by action', function () {
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    ActivityLog::factory()->forAgent($agent->id)->create([
        'user_id' => $this->admin->id,
        'action' => 'created',
    ]);

    ActivityLog::factory()->forAgent($agent->id)->create([
        'user_id' => $this->admin->id,
        'action' => 'updated',
    ]);

    $this->actingAs($agent)
        ->getJson(route('agent.activity-logs.index', ['action' => 'updated']))
        ->assertOk()
        ->assertJsonStructure(['html', 'pagination']);
});

it('agent dashboard shows recent activities', function () {
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    ActivityLog::factory()->count(2)->forAgent($agent->id)->create([
        'user_id' => $this->admin->id,
    ]);

    $this->actingAs($agent)
        ->get(route('agent.dashboard'))
        ->assertOk()
        ->assertViewHas('recentActivities', fn ($activities) => $activities->count() === 2);
});
