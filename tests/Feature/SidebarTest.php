<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
});

test('sidebar renders super admin menu items', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertStatus(200);
    // Check that Super Admin specific menu items exist
    $response->assertSee('Dashboard');
    $response->assertSee('Agents');
    $response->assertSee('Hotels');
    $response->assertSee('Transfers');
    $response->assertSee('Bookings');
});

test('sidebar renders agent menu items', function () {
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    $response = $this->actingAs($agent)->get(route('agent.dashboard'));

    $response->assertStatus(200);
    // Check that Agent specific menu items exist
    $response->assertSee('Dashboard');
    $response->assertSee('Hotels');
    $response->assertSee('Transfers');
    $response->assertSee('Bookings');
    // Booking is a submenu under Hotels and Transfers in agent menu
    $response->assertSee('Booking');
});
