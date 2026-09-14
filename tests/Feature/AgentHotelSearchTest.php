<?php

use App\Models\Hotel;
use App\Models\HotelRoomSlot;
use App\Models\TransferLocation;
use App\Models\User;
use App\Notifications\HotelBookingNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

    $this->agent = User::factory()->create();
    $this->agent->assignRole('Agent');

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');

    $this->location = TransferLocation::factory()->create([
        'name' => 'Dubai',
        'type' => 'city',
        'is_active' => true,
    ]);

    $this->hotel = Hotel::factory()->create([
        'name' => 'Marina Grand Resort',
        'location_id' => $this->location->id,
        'star_rating' => 5,
        'is_active' => true,
    ]);

    $this->slot = HotelRoomSlot::factory()->create([
        'hotel_id' => $this->hotel->id,
        'name' => 'Deluxe Room',
        'capacity' => 2,
        'available_qty' => 5,
        'price_per_night' => 450,
        'is_active' => true,
    ]);
});

it('allows agent to view the search catalog', function () {
    $this->actingAs($this->agent)
        ->get(route('agent.hotels.index'))
        ->assertOk()
        ->assertViewIs('agent.hotels.index')
        ->assertSee('Hotels')
        ->assertSee('Marina Grand Resort');
});

it('restricts agents from mutating hotel inventory', function () {
    $this->actingAs($this->agent)
        ->postJson(route('admin.hotels.store'), [
            'name' => 'Unauthorized Hotel',
            'location_id' => $this->location->id,
            'star_rating' => 4,
        ])
        ->assertStatus(403);
});

it('allows super admin to create hotels via admin route', function () {
    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.hotels.store'), [
            'name' => 'Palm Luxury Resort',
            'location_id' => $this->location->id,
            'star_rating' => 5,
            'address' => 'Palm Jumeirah',
            'is_active' => true,
        ]);

    $response->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('hotels', ['name' => 'Palm Luxury Resort']);
});

it('allows agent to book a room and dispatches notifications', function () {
    Notification::fake();

    $checkIn = date('Y-m-d', strtotime('+2 days'));
    $checkOut = date('Y-m-d', strtotime('+4 days'));

    $response = $this->actingAs($this->agent)
        ->postJson(route('agent.hotels.book'), [
            'hotel_room_slot_id' => $this->slot->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'johndoe@example.com',
            'customer_phone' => '+971501234567',
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
            'rooms_count' => 1,
        ]);

    $response->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('hotel_bookings', [
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'John Doe',
        'customer_email' => 'johndoe@example.com',
    ]);

    Notification::assertSentOnDemand(
        HotelBookingNotification::class,
        function ($notification, $channels, $notifiable) {
            return $notifiable->routes['mail'] === 'johndoe@example.com';
        }
    );
});

it('filters hotels by month and season type on agent catalog search', function () {
    // Create a second hotel with Low season in June
    $hotelJune = Hotel::factory()->create([
        'name' => 'Summer Escape Hotel',
        'location_id' => $this->location->id,
        'star_rating' => 4,
        'is_active' => true,
    ]);

    HotelRoomSlot::factory()->create([
        'hotel_id' => $hotelJune->id,
        'name' => 'Summer Discount Room',
        'month' => 'June',
        'season_type' => 'Low',
        'is_active' => true,
    ]);

    // Give our initial hotel slot a December High season
    $this->slot->update([
        'month' => 'December',
        'season_type' => 'High',
    ]);

    // Search for December + High
    $this->actingAs($this->agent)
        ->get(route('agent.hotels.index', ['month' => 'December', 'season_type' => 'High']))
        ->assertOk()
        ->assertSee('Marina Grand Resort')
        ->assertDontSee('Summer Escape Hotel');

    // Search for June + Low
    $this->actingAs($this->agent)
        ->get(route('agent.hotels.index', ['month' => 'June', 'season_type' => 'Low']))
        ->assertOk()
        ->assertSee('Summer Escape Hotel')
        ->assertDontSee('Marina Grand Resort');
});
