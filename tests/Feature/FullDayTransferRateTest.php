<?php

use App\Models\FullDayTransferRate;
use App\Models\TransferLocation;
use App\Models\User;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');

    $this->locationFrom = TransferLocation::factory()->create(['name' => 'Dubai', 'type' => 'city']);
    $this->locationTo = TransferLocation::factory()->create(['name' => 'Abu Dhabi', 'type' => 'city']);
    $this->vehicle = VehicleType::factory()->create(['name' => 'Sedan']);
});

it('can view the transfers index page with fullday tab', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.transfers.index', ['tab' => 'fullday']))
        ->assertOk()
        ->assertViewIs('admin.transfers.index')
        ->assertSee('Full-day Booking');
});

it('can create a full-day transfer rate via ajax', function () {
    $payload = [
        'from_location_id' => $this->locationFrom->id,
        'to_location_id' => $this->locationTo->id,
        'vehicle_type_id' => $this->vehicle->id,
        'fare_type' => 'half_day',
        'price' => 600,
        'currency' => 'AED',
        'is_active' => '1',
    ];

    $this->actingAs($this->admin)
        ->postJson(route('admin.transfers.full-day-rates.store'), $payload)
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('full_day_transfer_rates', [
        'from_location_id' => $this->locationFrom->id,
        'to_location_id' => $this->locationTo->id,
        'vehicle_type_id' => $this->vehicle->id,
        'price' => 600,
        'fare_type' => 'half_day',
    ]);
});

it('can update a full-day transfer rate via ajax', function () {
    $rate = FullDayTransferRate::factory()->create([
        'from_location_id' => $this->locationFrom->id,
        'to_location_id' => $this->locationTo->id,
        'vehicle_type_id' => $this->vehicle->id,
        'price' => 500,
        'fare_type' => 'full_day',
    ]);

    $this->actingAs($this->admin)
        ->putJson(route('admin.transfers.full-day-rates.update', $rate), [
            'from_location_id' => $this->locationFrom->id,
            'to_location_id' => $this->locationTo->id,
            'vehicle_type_id' => $this->vehicle->id,
            'price' => 750,
            'fare_type' => 'half_day',
            'currency' => 'AED',
            'is_active' => '1',
        ])
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('full_day_transfer_rates', [
        'id' => $rate->id,
        'price' => 750,
        'fare_type' => 'half_day',
    ]);
});

it('can toggle full-day status via ajax', function () {
    $rate = FullDayTransferRate::factory()->create([
        'from_location_id' => $this->locationFrom->id,
        'to_location_id' => $this->locationTo->id,
        'vehicle_type_id' => $this->vehicle->id,
        'is_active' => true,
    ]);

    $this->actingAs($this->admin)
        ->patchJson(route('admin.transfers.full-day-rates.toggle-status', $rate))
        ->assertOk()
        ->assertJsonFragment(['success' => true, 'is_active' => false]);

    $this->assertDatabaseHas('full_day_transfer_rates', ['id' => $rate->id, 'is_active' => false]);
});

it('can delete a full-day transfer rate via ajax', function () {
    $rate = FullDayTransferRate::factory()->create([
        'from_location_id' => $this->locationFrom->id,
        'to_location_id' => $this->locationTo->id,
        'vehicle_type_id' => $this->vehicle->id,
    ]);

    $this->actingAs($this->admin)
        ->deleteJson(route('admin.transfers.full-day-rates.destroy', $rate))
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseMissing('full_day_transfer_rates', ['id' => $rate->id]);
});

it('can create a full-day transfer rate with vehicle model', function () {
    $vehicleModel = VehicleModel::factory()->create([
        'vehicle_type_id' => $this->vehicle->id,
        'name' => 'Toyota Camry',
    ]);

    $payload = [
        'from_location_id' => $this->locationFrom->id,
        'to_location_id' => $this->locationTo->id,
        'vehicle_type_id' => $this->vehicle->id,
        'vehicle_model_id' => $vehicleModel->id,
        'fare_type' => 'half_day',
        'price' => 600,
        'currency' => 'AED',
        'is_active' => '1',
    ];

    $this->actingAs($this->admin)
        ->postJson(route('admin.transfers.full-day-rates.store'), $payload)
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    $this->assertDatabaseHas('full_day_transfer_rates', [
        'from_location_id' => $this->locationFrom->id,
        'to_location_id' => $this->locationTo->id,
        'vehicle_type_id' => $this->vehicle->id,
        'vehicle_model_id' => $vehicleModel->id,
        'price' => 600,
    ]);
});

it('can create a vehicle model inline', function () {
    $payload = [
        'vehicle_type_id' => $this->vehicle->id,
        'name' => 'Honda Civic',
    ];

    $this->actingAs($this->admin)
        ->postJson(route('admin.transfers.vehicle-models.store'), $payload)
        ->assertOk()
        ->assertJsonFragment(['success' => true])
        ->assertJsonPath('vehicle_model.name', 'Honda Civic')
        ->assertJsonPath('vehicle_model.vehicle_type_id', $this->vehicle->id);

    $this->assertDatabaseHas('vehicle_models', [
        'vehicle_type_id' => $this->vehicle->id,
        'name' => 'Honda Civic',
    ]);
});
