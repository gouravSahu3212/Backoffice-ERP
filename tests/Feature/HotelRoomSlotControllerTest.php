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

it('allows agent to store a room slot for a hotel', function () {
    $payload = [
        'name' => 'Deluxe Ocean View Room',
        'capacity' => 4,
        'available_qty' => 15,
        'price_per_night' => 350.50,
        'currency' => 'AED',
        'is_active' => true,
    ];

    $response = $this->actingAs($this->agent)
        ->postJson(route('agent.hotels.slots.store', $this->hotel), $payload);

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
    $response = $this->actingAs($this->agent)
        ->postJson(route('agent.hotels.slots.store', $this->hotel), []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'price_per_night']);
});

it('allows agent to update a room slot', function () {
    $slot = HotelRoomSlot::factory()->create([
        'hotel_id' => $this->hotel->id,
        'name' => 'Standard Room',
        'price_per_night' => 200,
    ]);

    $response = $this->actingAs($this->agent)
        ->putJson(route('agent.hotels.slots.update', [$this->hotel, $slot]), [
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

it('allows agent to toggle room slot status', function () {
    $slot = HotelRoomSlot::factory()->create([
        'hotel_id' => $this->hotel->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->agent)
        ->patchJson(route('agent.hotels.slots.toggle-status', [$this->hotel, $slot]));

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

it('allows agent to delete a room slot', function () {
    $slot = HotelRoomSlot::factory()->create([
        'hotel_id' => $this->hotel->id,
    ]);

    $response = $this->actingAs($this->agent)
        ->deleteJson(route('agent.hotels.slots.destroy', [$this->hotel, $slot]));

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
