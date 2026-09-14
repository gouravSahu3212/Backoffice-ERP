<?php

use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\HotelRoomSlot;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);

    $this->agent = User::factory()->create();
    $this->agent->assignRole('Agent');

    $this->otherAgent = User::factory()->create();
    $this->otherAgent->assignRole('Agent');

    $this->hotel = Hotel::factory()->create(['name' => 'Grand Plaza Resort']);
    $this->slot = HotelRoomSlot::factory()->create([
        'hotel_id' => $this->hotel->id,
        'name' => 'Executive Suite',
    ]);
});

it('restricts guests from accessing agent hotel bookings page', function () {
    $this->get(route('agent.hotel-bookings.index'))
        ->assertRedirect(route('login'));
});

it('allows agent to view their hotel bookings page', function () {
    $booking = HotelBooking::create([
        'booking_reference' => 'HB-REF12345',
        'user_id' => $this->agent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '+966500000000',
        'check_in' => now()->addDays(2)->format('Y-m-d'),
        'check_out' => now()->addDays(4)->format('Y-m-d'),
        'guests' => 2,
        'rooms_count' => 1,
        'price_per_night' => 300,
        'total_price' => 600,
        'currency' => 'SAR',
        'status' => 'confirmed',
    ]);

    $this->actingAs($this->agent)
        ->get(route('agent.hotel-bookings.index'))
        ->assertOk()
        ->assertViewIs('agent.hotels.bookings.index')
        ->assertSee('Hotel Bookings')
        ->assertSee('HB-REF12345')
        ->assertSee('John Doe')
        ->assertSee('Grand Plaza Resort');
});

it('only shows agent their own hotel bookings', function () {
    $myBooking = HotelBooking::create([
        'booking_reference' => 'HB-MY123456',
        'user_id' => $this->agent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'My Customer',
        'customer_email' => 'my@example.com',
        'check_in' => now()->addDays(1)->format('Y-m-d'),
        'check_out' => now()->addDays(2)->format('Y-m-d'),
        'guests' => 1,
        'rooms_count' => 1,
        'price_per_night' => 200,
        'total_price' => 200,
        'currency' => 'SAR',
        'status' => 'confirmed',
    ]);

    $otherBooking = HotelBooking::create([
        'booking_reference' => 'HB-OTHER999',
        'user_id' => $this->otherAgent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'Other Customer',
        'customer_email' => 'other@example.com',
        'check_in' => now()->addDays(1)->format('Y-m-d'),
        'check_out' => now()->addDays(2)->format('Y-m-d'),
        'guests' => 1,
        'rooms_count' => 1,
        'price_per_night' => 200,
        'total_price' => 200,
        'currency' => 'SAR',
        'status' => 'confirmed',
    ]);

    $this->actingAs($this->agent)
        ->get(route('agent.hotel-bookings.index'))
        ->assertOk()
        ->assertSee('HB-MY123456')
        ->assertDontSee('HB-OTHER999');
});

it('filters hotel bookings by search query', function () {
    HotelBooking::create([
        'booking_reference' => 'HB-ALPHA111',
        'user_id' => $this->agent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'Alice Smith',
        'customer_email' => 'alice@example.com',
        'check_in' => now()->addDays(1)->format('Y-m-d'),
        'check_out' => now()->addDays(3)->format('Y-m-d'),
        'guests' => 2,
        'rooms_count' => 1,
        'price_per_night' => 150,
        'total_price' => 300,
        'currency' => 'SAR',
        'status' => 'confirmed',
    ]);

    HotelBooking::create([
        'booking_reference' => 'HB-BETA222',
        'user_id' => $this->agent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'Bob Jones',
        'customer_email' => 'bob@example.com',
        'check_in' => now()->addDays(1)->format('Y-m-d'),
        'check_out' => now()->addDays(3)->format('Y-m-d'),
        'guests' => 2,
        'rooms_count' => 1,
        'price_per_night' => 150,
        'total_price' => 300,
        'currency' => 'SAR',
        'status' => 'confirmed',
    ]);

    $this->actingAs($this->agent)
        ->get(route('agent.hotel-bookings.index', ['search' => 'Alice']))
        ->assertOk()
        ->assertSee('HB-ALPHA111')
        ->assertDontSee('HB-BETA222');
});
