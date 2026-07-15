<?php

use App\Models\TransferBooking;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
});

test('guest cannot access transfer bookings index', function () {
    $this->get(route('admin.transfers.bookings.index'))->assertRedirect(route('login'));
});

test('agent cannot access transfer bookings index', function () {
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    $this->actingAs($agent)
        ->get(route('admin.transfers.bookings.index'))
        ->assertStatus(403);
});

test('super admin can view transfer bookings index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    TransferBooking::create([
        'booking_reference' => 'TB-1001',
        'guest_name' => 'Amina Khan',
        'guest_email' => 'amina@example.com',
        'guest_phone' => '+966500000000',
        'pickup_location' => 'Riyadh Airport',
        'dropoff_location' => 'Makkah Hotel',
        'vehicle_type' => 'Sedan',
        'transfer_date' => '2026-08-10',
        'pickup_time' => '08:00',
        'passengers' => 2,
        'status' => 'pending',
        'total_amount' => 180.00,
        'currency' => 'SAR',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.transfers.bookings.index'))
        ->assertStatus(200)
        ->assertSee('Transfer Bookings')
        ->assertSee('TB-1001');
});

test('super admin can view transfer booking details', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $booking = TransferBooking::create([
        'booking_reference' => 'TB-2002',
        'guest_name' => 'Omar Haddad',
        'guest_email' => 'omar@example.com',
        'guest_phone' => '+966500000001',
        'pickup_location' => 'Jeddah Airport',
        'dropoff_location' => 'Jeddah City Center',
        'vehicle_type' => 'Van',
        'transfer_date' => '2026-08-12',
        'pickup_time' => '10:30',
        'passengers' => 4,
        'status' => 'confirmed',
        'total_amount' => 240.00,
        'currency' => 'SAR',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.transfers.bookings.show', $booking))
        ->assertStatus(200)
        ->assertSee('TB-2002')
        ->assertSee('Omar Haddad');
});
