<?php

use App\Models\Tour;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
});

test('guest cannot access tours index', function () {
    $this->get(route('admin.tours.index'))->assertRedirect(route('login'));
});

test('agent cannot access tours index', function () {
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    $this->actingAs($agent)->get(route('admin.tours.index'))->assertStatus(403);
});

test('super admin can view tours index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $this->actingAs($admin)->get(route('admin.tours.index'))->assertStatus(200)->assertSee('Tours');
});

test('super admin can create a tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->post(route('admin.tours.store'), [
        'title' => 'Saudi Arabia: Complete Tour',
        'location' => 'Saudi Arabia',
        'days' => 9,
        'hotel_rating' => 4,
        'currency' => 'USD',
        'retail_price' => 4089,
        'agent_price' => 3271,
        'summary' => 'An amazing tour.',
        'description' => 'Full tour description.',
        'itinerary' => "Day 1: Arrival\nDay 2: City Tour",
        'highlights' => "Professional guide\nAll breakfasts",
        'whats_included' => "Airport transfers\nAccommodation",
        'image_urls' => '',
        'departure_months' => [
            ['date' => '2026-10-16', 'slots' => 10],
            ['date' => '2026-10-30', 'slots' => 10],
        ],
        'max_capacity' => 20,
        'is_active' => 1,
    ]);

    $response->assertRedirect(route('admin.tours.index'));
    $this->assertDatabaseHas('tours', [
        'title' => 'Saudi Arabia: Complete Tour',
        'location' => 'Saudi Arabia',
    ]);
});

test('super admin can toggle tour status', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $tour = Tour::factory()->create(['is_active' => true]);

    $this->actingAs($admin)
        ->patchJson(route('admin.tours.toggle-status', $tour))
        ->assertJson(['success' => true, 'is_active' => false]);

    $this->assertDatabaseHas('tours', ['id' => $tour->id, 'is_active' => false]);
});

test('super admin can delete a tour', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $tour = Tour::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.tours.destroy', $tour))
        ->assertRedirect();

    $this->assertDatabaseMissing('tours', ['id' => $tour->id]);
});
