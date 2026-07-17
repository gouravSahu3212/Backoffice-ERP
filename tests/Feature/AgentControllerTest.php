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
        'username' => 'test_agent',
        'email' => 'agent@example.com',
        'phone' => '+966500000000',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('admin.agents.index'));
    $this->assertDatabaseHas('users', [
        'name' => 'Test Agent',
        'email' => 'agent@example.com',
        'phone' => '+966500000000',
    ]);
});

test('super admin can update agent', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    $response = $this->actingAs($admin)->put(route('admin.agents.update', $agent), [
        'name' => 'Updated Agent Name',
        'username' => 'updated_agent',
        'email' => 'updated-agent@example.com',
        'phone' => '+966500000001',
    ]);

    $response->assertRedirect(route('admin.agents.index'));
    $this->assertDatabaseHas('users', [
        'id' => $agent->id,
        'name' => 'Updated Agent Name',
        'email' => 'updated-agent@example.com',
        'phone' => '+966500000001',
    ]);
});

test('super admin cannot create agent with invalid phone format', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->post(route('admin.agents.store'), [
        'name' => 'Test Agent',
        'username' => 'test_agent_invalid',
        'email' => 'invalid-phone@example.com',
        'phone' => '1234567', // too short / wrong format
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors('phone');
});

test('super admin can create agent with null phone', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->post(route('admin.agents.store'), [
        'name' => 'Test Agent',
        'username' => 'test_agent_null',
        'email' => 'null-phone@example.com',
        'phone' => null,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('admin.agents.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'null-phone@example.com',
        'phone' => null,
    ]);
});

test('super admin can create agent with valid country phone numbers', function (string $phone) {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $username = 'agent_'.uniqid();
    $email = $username.'@example.com';

    $response = $this->actingAs($admin)->post(route('admin.agents.store'), [
        'name' => 'Test Agent',
        'username' => $username,
        'email' => $email,
        'phone' => $phone,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect(route('admin.agents.index'));
    $this->assertDatabaseHas('users', [
        'email' => $email,
        'phone' => $phone,
    ]);
})->with([
    'Saudi (+966)' => '+966 50 123 4567',
    'Saudi (05)' => '0501234567',
    'Jordan (+962)' => '+962 7 9123 4567',
    'Jordan (07)' => '0781234567',
    'Morocco (+212)' => '+212 6 1234 5678',
    'Morocco (06)' => '0612345678',
    'Egypt (+20)' => '+20 10 1234 5678',
    'Egypt (01)' => '01112345678',
    'Turkey (+90)' => '+90 512 345 6789',
    'Turkey (05)' => '05321234567',
    'UAE (+971)' => '+971 50 123 4567',
    'UAE (05)' => '0541234567',
]);
