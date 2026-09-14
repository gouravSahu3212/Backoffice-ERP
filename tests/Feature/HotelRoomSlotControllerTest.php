<?php

use App\Models\Hotel;
use App\Models\HotelRoomSlot;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

    $this->agent = User::factory()->create();
    $this->agent->assignRole('Agent');

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');

    $this->hotel = Hotel::factory()->create([
        'name' => 'Deluxe Beach Resort',
    ]);
});

it('allows admin to store a room slot for a hotel', function () {
    $payload = [
        'name' => 'Deluxe Ocean View Room',
        'capacity' => 4,
        'available_qty' => 15,
        'price_per_night' => 350.50,
        'currency' => 'AED',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.hotels.slots.store', $this->hotel), $payload);

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'message' => 'Room slot added successfully.',
        ]);

    $this->assertDatabaseHas('hotel_room_slots', [
        'hotel_id' => $this->hotel->id,
        'name' => 'Deluxe Ocean View Room',
        'capacity' => 4,
        'available_qty' => 15,
        'price_per_night' => 350.50,
        'currency' => 'AED',
        'is_active' => true,
    ]);
});

it('validates required fields when creating room slot', function () {
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.hotels.slots.store', $this->hotel), []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'price_per_night']);
});

it('allows admin to update a room slot', function () {
    $slot = HotelRoomSlot::factory()->create([
        'hotel_id' => $this->hotel->id,
        'name' => 'Standard Room',
        'price_per_night' => 200,
    ]);

    $response = $this->actingAs($this->admin)
        ->putJson(route('admin.hotels.slots.update', [$this->hotel, $slot]), [
            'name' => 'Executive Suite',
            'capacity' => 2,
            'available_qty' => 5,
            'price_per_night' => 450,
            'currency' => 'USD',
            'is_active' => true,
        ]);

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'message' => 'Room slot updated successfully.',
        ]);

    $this->assertDatabaseHas('hotel_room_slots', [
        'id' => $slot->id,
        'name' => 'Executive Suite',
        'price_per_night' => 450,
        'currency' => 'USD',
    ]);
});

it('allows admin to toggle room slot status', function () {
    $slot = HotelRoomSlot::factory()->create([
        'hotel_id' => $this->hotel->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->admin)
        ->patchJson(route('admin.hotels.slots.toggle-status', [$this->hotel, $slot]));

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'is_active' => false,
        ]);

    $this->assertDatabaseHas('hotel_room_slots', [
        'id' => $slot->id,
        'is_active' => false,
    ]);
});

it('allows admin to delete a room slot', function () {
    $slot = HotelRoomSlot::factory()->create([
        'hotel_id' => $this->hotel->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->deleteJson(route('admin.hotels.slots.destroy', [$this->hotel, $slot]));

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'message' => 'Room slot deleted successfully.',
        ]);

    $this->assertDatabaseMissing('hotel_room_slots', [
        'id' => $slot->id,
    ]);
});

it('allows admin to create a room slot for a hotel', function () {
    $payload = [
        'name' => 'Admin Presidential Suite',
        'capacity' => 6,
        'available_qty' => 2,
        'price_per_night' => 1200,
        'currency' => 'AED',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.hotels.slots.store', $this->hotel), $payload);

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'message' => 'Room slot added successfully.',
        ]);

    $this->assertDatabaseHas('hotel_room_slots', [
        'hotel_id' => $this->hotel->id,
        'name' => 'Admin Presidential Suite',
    ]);
});

it('allows admin to store a room slot with month and season type', function () {
    $payload = [
        'name' => 'High Season Suite',
        'capacity' => 2,
        'available_qty' => 10,
        'price_per_night' => 800,
        'currency' => 'AED',
        'month' => 'December',
        'season_type' => 'High',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.hotels.slots.store', $this->hotel), $payload);

    $response->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'message' => 'Room slot added successfully.',
        ]);

    $this->assertDatabaseHas('hotel_room_slots', [
        'hotel_id' => $this->hotel->id,
        'name' => 'High Season Suite',
        'month' => 'December',
        'season_type' => 'High',
    ]);
});

it('clears season_type if month is empty when storing or updating room slot', function () {
    $payload = [
        'name' => 'Shoulder Season Room',
        'capacity' => 2,
        'available_qty' => 5,
        'price_per_night' => 300,
        'currency' => 'AED',
        'month' => null,
        'season_type' => 'Shoulder',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.hotels.slots.store', $this->hotel), $payload);

    $response->assertOk();

    $this->assertDatabaseHas('hotel_room_slots', [
        'hotel_id' => $this->hotel->id,
        'name' => 'Shoulder Season Room',
        'month' => null,
        'season_type' => null,
    ]);
});
