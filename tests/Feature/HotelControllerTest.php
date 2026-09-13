<?php

use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\TransferLocation;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

    $this->agent = User::factory()->create();
    $this->agent->assignRole('Agent');

    $this->location = TransferLocation::factory()->create([
        'name' => 'Dubai',
        'type' => 'city',
        'is_active' => true,
    ]);

    $this->amenity = Amenity::factory()->create([
        'name' => 'WiFi',
        'is_active' => true,
    ]);
});

it('restricts guests from accessing agent hotels page', function () {
    $this->get(route('agent.hotels.index'))
        ->assertRedirect(route('login'));
});

it('allows agent to view the hotels page', function () {
    Hotel::factory()->create([
        'name' => 'Marina Grand Resort',
        'location_id' => $this->location->id,
        'star_rating' => 5,
    ]);

    $this->actingAs($this->agent)
        ->get(route('agent.hotels.index'))
        ->assertOk()
        ->assertViewIs('agent.hotels.index')
        ->assertSee('Hotels')
        ->assertSee('Marina Grand Resort');
});

it('filters hotels via ajax', function () {
    $hotel = Hotel::factory()->create([
        'name' => 'JBR Beach Hotel',
        'location_id' => $this->location->id,
        'star_rating' => 4,
    ]);

    $this->actingAs($this->agent)
        ->getJson(route('agent.hotels.index', ['search' => 'JBR']))
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'JBR Beach Hotel',
        ]);
});

it('allows agent to store a new hotel', function () {
    $payload = [
        'name' => 'Palm Luxury Hotel',
        'location_id' => $this->location->id,
        'address' => 'Palm Jumeirah, Dubai',
        'description' => '5 star resort with private beach',
        'terms_and_conditions' => 'Standard cancellation policy applies.',
        'star_rating' => 5,
        'amenities' => ['WiFi', 'Pool', 'Spa'],
        'is_active' => true,
        'is_featured' => true,
    ];

    $response = $this->actingAs($this->agent)
        ->postJson(route('agent.hotels.store'), $payload);

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'message' => 'Hotel created successfully.',
        ]);

    $this->assertDatabaseHas('hotels', [
        'name' => 'Palm Luxury Hotel',
        'location_id' => $this->location->id,
        'star_rating' => 5,
        'is_featured' => true,
    ]);
});

it('allows agent to update an existing hotel', function () {
    $hotel = Hotel::factory()->create([
        'name' => 'Old Hotel Name',
        'location_id' => $this->location->id,
        'star_rating' => 3,
    ]);

    $response = $this->actingAs($this->agent)
        ->putJson(route('agent.hotels.update', $hotel), [
            'name' => 'Renovated Luxury Hotel',
            'location_id' => $this->location->id,
            'address' => 'Updated Address',
            'description' => 'Updated Description',
            'terms_and_conditions' => 'Updated Terms',
            'star_rating' => 5,
            'amenities' => ['WiFi', 'Gym'],
            'is_active' => true,
            'is_featured' => false,
        ]);

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'message' => 'Hotel updated successfully.',
        ]);

    $this->assertDatabaseHas('hotels', [
        'id' => $hotel->id,
        'name' => 'Renovated Luxury Hotel',
        'star_rating' => 5,
    ]);
});

it('allows agent to toggle hotel status', function () {
    $hotel = Hotel::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->agent)
        ->patchJson(route('agent.hotels.toggle-status', $hotel));

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'is_active' => false,
        ]);

    $this->assertDatabaseHas('hotels', [
        'id' => $hotel->id,
        'is_active' => false,
    ]);
});

it('allows agent to store a location dynamically', function () {
    $response = $this->actingAs($this->agent)
        ->postJson(route('agent.hotels.locations.store'), [
            'name' => 'Ras Al Khaimah',
        ]);

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'name' => 'Ras Al Khaimah',
        ]);

    $this->assertDatabaseHas('transfer_locations', [
        'name' => 'Ras Al Khaimah',
        'type' => 'city',
    ]);
});

it('allows agent to store an amenity dynamically', function () {
    $response = $this->actingAs($this->agent)
        ->postJson(route('agent.hotels.amenities.store'), [
            'name' => 'Infinity Pool',
        ]);

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'name' => 'Infinity Pool',
        ]);

    $this->assertDatabaseHas('amenities', [
        'name' => 'Infinity Pool',
    ]);
});

it('allows agent to delete a hotel', function () {
    $hotel = Hotel::factory()->create();

    $response = $this->actingAs($this->agent)
        ->deleteJson(route('agent.hotels.destroy', $hotel));

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'message' => 'Hotel deleted successfully.',
        ]);

    $this->assertDatabaseMissing('hotels', [
        'id' => $hotel->id,
    ]);
});
