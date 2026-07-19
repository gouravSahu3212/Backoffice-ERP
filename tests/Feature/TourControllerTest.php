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

test('tour creation fails if departure dates contain duplicate dates', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $response = $this->actingAs($admin)->post(route('admin.tours.store'), [
        'title' => 'Saudi Arabia: Complete Tour',
        'location' => 'Saudi Arabia',
        'days' => 9,
        'hotel_rating' => 4,
        'currency' => 'USD',
        'retail_price' => 4000,
        'agent_price' => 3000,
        'departure_months' => [
            ['date' => '2026-10-16', 'slots' => 10],
            ['date' => '2026-10-16', 'slots' => 5], // Duplicate date
        ],
    ]);

    $response->assertSessionHasErrors('departure_months.1.date');
});

test('tour creation fails if retail price is not greater than agent price', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    // Case 1: Retail price is equal to agent price
    $response = $this->actingAs($admin)->post(route('admin.tours.store'), [
        'title' => 'Saudi Arabia: Complete Tour',
        'location' => 'Saudi Arabia',
        'days' => 9,
        'hotel_rating' => 4,
        'currency' => 'USD',
        'retail_price' => 3000,
        'agent_price' => 3000,
    ]);
    $response->assertSessionHasErrors('retail_price');

    // Case 2: Retail price is less than agent price
    $response = $this->actingAs($admin)->post(route('admin.tours.store'), [
        'title' => 'Saudi Arabia: Complete Tour',
        'location' => 'Saudi Arabia',
        'days' => 9,
        'hotel_rating' => 4,
        'currency' => 'USD',
        'retail_price' => 2500,
        'agent_price' => 3000,
    ]);
    $response->assertSessionHasErrors('retail_price');
});
