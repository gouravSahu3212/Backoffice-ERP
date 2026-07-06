<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
});

test('guest cannot access agent create form', function () {
    $response = $this->get(route('admin.agents.create'));
    $response->assertRedirect(route('login'));
});

test('non-admin user cannot access agent create form', function () {
    $user = User::factory()->create();
    $user->assignRole('Agent');

    $response = $this->actingAs($user)->get(route('admin.agents.create'));
    $response->assertStatus(403);
});

test('super admin can access agent create form', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->get(route('admin.agents.create'));
    $response->assertStatus(200);
});

test('super admin can create agent', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->post(route('admin.agents.store'), [
        'name' => 'Test Agent',
        'email' => 'agent@example.com',
        'phone' => '1234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('admin.agents.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'Test Agent',
        'email' => 'agent@example.com',
        'phone' => '1234567890',
    ]);
});

test('super admin can update agent', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    $response = $this->actingAs($admin)->put(route('admin.agents.update', $agent), [
        'name' => 'Updated Agent Name',
        'email' => 'updated-agent@example.com',
        'phone' => '0987654321',
    ]);

    $response->assertRedirect(route('admin.agents.index'));
    $this->assertDatabaseHas('users', [
        'id' => $agent->id,
        'name' => 'Updated Agent Name',
        'email' => 'updated-agent@example.com',
        'phone' => '0987654321',
    ]);
});
