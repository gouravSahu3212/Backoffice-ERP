<?php

use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\HotelRoomSlot;
use App\Models\User;
use App\Notifications\HotelBookingReceivedNotification;
use App\Notifications\HotelBookingStatusUpdatedNotification;
use App\Repositories\HotelRepository;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');

    $this->agent = User::factory()->create(['name' => 'Agent Smith']);
    $this->agent->assignRole('Agent');

    $this->hotel = Hotel::factory()->create(['name' => 'Marina Grand Resort']);
    $this->slot = HotelRoomSlot::factory()->create([
        'hotel_id' => $this->hotel->id,
        'name' => 'Deluxe Room',
        'price_per_night' => 450,
        'available_qty' => 10,
    ]);
});

it('creates hotel room booking request with status new and notifies super admin', function () {
    Notification::fake();

    $response = $this->actingAs($this->agent)
        ->postJson(route('agent.hotels.book'), [
            'hotel_room_slot_id' => $this->slot->id,
            'customer_name' => 'Mohammed Ali',
            'customer_email' => 'mohammed@email.com',
            'customer_phone' => '+971551234567',
            'check_in' => now()->addDays(2)->format('Y-m-d'),
            'check_out' => now()->addDays(5)->format('Y-m-d'),
            'guests' => 2,
            'rooms_count' => 1,
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('hotel_bookings', [
        'customer_name' => 'Mohammed Ali',
        'status' => 'new',
    ]);

    Notification::assertSentTo(
        $this->admin,
        HotelBookingReceivedNotification::class
    );
});

it('allows super admin to list all hotel bookings', function () {
    $booking = HotelBooking::create([
        'booking_reference' => 'BK-001',
        'user_id' => $this->agent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'Mohammed Ali',
        'customer_email' => 'mohammed@email.com',
        'check_in' => now()->addDays(2)->format('Y-m-d'),
        'check_out' => now()->addDays(5)->format('Y-m-d'),
        'guests' => 2,
        'rooms_count' => 1,
        'price_per_night' => 450,
        'total_price' => 1350,
        'currency' => 'AED',
        'status' => 'new',
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.hotels.bookings.index'))
        ->assertOk()
        ->assertSee('Hotel Bookings')
        ->assertSee('BK-001')
        ->assertSee('Mohammed Ali')
        ->assertSee('Marina Grand Resort');
});

it('allows super admin to update hotel booking status and notifies agent', function () {
    Notification::fake();

    $booking = HotelBooking::create([
        'booking_reference' => 'BK-002',
        'user_id' => $this->agent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'Emily Johnson',
        'customer_email' => 'emily@example.com',
        'check_in' => now()->addDays(1)->format('Y-m-d'),
        'check_out' => now()->addDays(3)->format('Y-m-d'),
        'guests' => 1,
        'rooms_count' => 1,
        'price_per_night' => 400,
        'total_price' => 800,
        'currency' => 'AED',
        'status' => 'new',
    ]);

    $response = $this->actingAs($this->admin)
        ->patchJson(route('admin.hotels.bookings.update-status', $booking), [
            'status' => 'confirmed',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 'confirmed',
        ]);

    $this->assertDatabaseHas('hotel_bookings', [
        'id' => $booking->id,
        'status' => 'confirmed',
    ]);

    Notification::assertSentTo(
        $this->agent,
        HotelBookingStatusUpdatedNotification::class
    );
});

it('shows payment as paid only when status is confirmed', function () {
    $booking = HotelBooking::create([
        'booking_reference' => 'BK-003',
        'user_id' => $this->agent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'Chen Wei',
        'customer_email' => 'chen@example.com',
        'customer_phone' => '+971500000000',
        'check_in' => '2026-02-20',
        'check_out' => '2026-02-22',
        'guests' => 2,
        'rooms_count' => 1,
        'price_per_night' => 380,
        'total_price' => 760,
        'currency' => 'AED',
        'status' => 'new',
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.hotels.bookings.show', $booking));

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'booking' => [
                'payment_status' => '-',
            ],
        ]);

    $booking->update(['status' => 'confirmed']);

    $responsePaid = $this->actingAs($this->admin)
        ->getJson(route('admin.hotels.bookings.show', $booking));

    $responsePaid->assertOk()
        ->assertJson([
            'success' => true,
            'booking' => [
                'payment_status' => 'paid',
            ],
        ]);
});

it('renders printable pdf view for hotel booking', function () {
    $booking = HotelBooking::create([
        'booking_reference' => 'BK-004',
        'user_id' => $this->agent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'Abhay Patel',
        'customer_email' => 'abhay@example.com',
        'check_in' => '2026-09-02',
        'check_out' => '2026-09-09',
        'guests' => 2,
        'rooms_count' => 1,
        'price_per_night' => 280,
        'total_price' => 1960,
        'currency' => 'AED',
        'status' => 'confirmed',
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.hotels.bookings.print', $booking))
        ->assertOk()
        ->assertSee('Hotel Booking Voucher')
        ->assertSee('BK-004')
        ->assertSee('Abhay Patel');
});

it('allows super admin to reject hotel booking and restores room slot availability', function () {
    $hotelRepo = app(HotelRepository::class);

    $checkIn = now()->addDays(5)->format('Y-m-d');
    $checkOut = now()->addDays(7)->format('Y-m-d');

    $booking = HotelBooking::create([
        'booking_reference' => 'BK-REJ001',
        'user_id' => $this->agent->id,
        'hotel_id' => $this->hotel->id,
        'hotel_room_slot_id' => $this->slot->id,
        'customer_name' => 'Sarah Connor',
        'customer_email' => 'sarah@example.com',
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'guests' => 2,
        'rooms_count' => 5,
        'price_per_night' => 450,
        'total_price' => 1800,
        'currency' => 'AED',
        'status' => 'new',
    ]);

    expect($hotelRepo->getSlotAvailableQty($this->slot->id, $checkIn, $checkOut))->toBe(5);

    $response = $this->actingAs($this->admin)
        ->patchJson(route('admin.hotels.bookings.update-status', $booking), [
            'status' => 'rejected',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 'rejected',
        ]);

    $this->assertDatabaseHas('hotel_bookings', [
        'id' => $booking->id,
        'status' => 'rejected',
    ]);

    expect($hotelRepo->getSlotAvailableQty($this->slot->id, $checkIn, $checkOut))->toBe(10);
});
