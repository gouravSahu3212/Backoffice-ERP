<?php

use App\Models\CityTransferRate;
use App\Models\TransferLocation;
use App\Models\User;
use App\Models\VehicleType;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');

    $this->from    = TransferLocation::factory()->create(['name' => 'Dubai', 'type' => 'city']);
    $this->to      = TransferLocation::factory()->create(['name' => 'Abu Dhabi', 'type' => 'city']);
    $this->vehicle = VehicleType::factory()->create(['name' => 'Sedan']);
});

it('can view the transfers index page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.transfers.index'))
        ->assertOk()
        ->assertViewIs('admin.transfers.index');
});

it('can create a city transfer rate via ajax', function () {
    $payload = [
        'from_location_id' => $this->from->id,
        'to_location_id'   => $this->to->id,
        'vehicle_type_id'  => $this->vehicle->id,
        'fare_type'        => 'fixed',
        'price'            => 250,
        'currency'         => 'AED',
        'is_active'        => '1',
    ];

    $this->actingAs($this->admin)
        ->postJson(route('admin.transfers.city-rates.store'), $payload)
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('city_transfer_rates', [
        'from_location_id' => $this->from->id,
        'to_location_id'   => $this->to->id,
        'price'            => 250,
    ]);
});

it('validates that from and to locations are different', function () {
    $payload = [
        'from_location_id' => $this->from->id,
        'to_location_id'   => $this->from->id, // same as from
        'vehicle_type_id'  => $this->vehicle->id,
        'fare_type'        => 'fixed',
        'price'            => 100,
        'currency'         => 'AED',
    ];

    $response = $this->actingAs($this->admin)
        ->postJson(route('admin.transfers.city-rates.store'), $payload);

    $response->assertStatus(422);
    $this->assertArrayHasKey('to_location_id', $response->json('errors'));
});

it('can update a city transfer rate via ajax', function () {
    $rate = CityTransferRate::factory()->create([
        'from_location_id' => $this->from->id,
        'to_location_id'   => $this->to->id,
        'vehicle_type_id'  => $this->vehicle->id,
        'price'            => 200,
    ]);

    $this->actingAs($this->admin)
        ->putJson(route('admin.transfers.city-rates.update', $rate), [
            'from_location_id' => $this->from->id,
            'to_location_id'   => $this->to->id,
            'vehicle_type_id'  => $this->vehicle->id,
            'fare_type'        => 'fixed',
            'price'            => 350,
            'currency'         => 'AED',
            'is_active'        => '1',
        ])
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('city_transfer_rates', ['id' => $rate->id, 'price' => 350]);
});

it('can toggle status via ajax', function () {
    $rate = CityTransferRate::factory()->create([
        'from_location_id' => $this->from->id,
        'to_location_id'   => $this->to->id,
        'vehicle_type_id'  => $this->vehicle->id,
        'is_active'        => true,
    ]);

    $this->actingAs($this->admin)
        ->patchJson(route('admin.transfers.city-rates.toggle-status', $rate))
        ->assertOk()
        ->assertJsonFragment(['success' => true, 'is_active' => false]);

    $this->assertDatabaseHas('city_transfer_rates', ['id' => $rate->id, 'is_active' => false]);
});

it('can delete a city transfer rate via ajax', function () {
    $rate = CityTransferRate::factory()->create([
        'from_location_id' => $this->from->id,
        'to_location_id'   => $this->to->id,
        'vehicle_type_id'  => $this->vehicle->id,
    ]);

    $this->actingAs($this->admin)
        ->deleteJson(route('admin.transfers.city-rates.destroy', $rate))
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseMissing('city_transfer_rates', ['id' => $rate->id]);
});

it('can create a new location inline', function () {
    $this->actingAs($this->admin)
        ->postJson(route('admin.transfers.locations.store'), ['name' => 'Al Ain', 'type' => 'city'])
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('transfer_locations', ['name' => 'Al Ain']);
});

it('can create a new vehicle type inline', function () {
    $this->actingAs($this->admin)
        ->postJson(route('admin.transfers.vehicle-types.store'), ['name' => 'Limousine'])
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('vehicle_types', ['name' => 'Limousine']);
});
