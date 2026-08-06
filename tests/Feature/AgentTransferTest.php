<?php

use App\Models\AirportTransferRate;
use App\Models\AirportTransferZone;
use App\Models\CityTransferRate;
use App\Models\FullDayTransferRate;
use App\Models\TransferLocation;
use App\Models\User;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

    $this->agent = User::factory()->create();
    $this->agent->assignRole('Agent');

    $this->fromLocation = TransferLocation::factory()->create(['name' => 'Dubai', 'type' => 'city']);
    $this->toLocation = TransferLocation::factory()->create(['name' => 'Abu Dhabi', 'type' => 'city']);
    $this->airport = TransferLocation::factory()->create(['name' => 'Dubai International Airport', 'type' => 'airport']);
    $this->zone = AirportTransferZone::factory()->create(['name' => 'Downtown Dubai']);
    $this->vehicleType = VehicleType::factory()->create(['name' => 'Sedan']);
    $this->vehicleModel = VehicleModel::factory()->create(['name' => 'Mercedes E-Class', 'vehicle_type_id' => $this->vehicleType->id]);
});

it('restricts guests from accessing agent transfers page', function () {
    $this->get(route('agent.transfers.index'))
        ->assertRedirect(route('login'));
});

it('allows agent to view the transfers page', function () {
    $this->actingAs($this->agent)
        ->get(route('agent.transfers.index'))
        ->assertOk()
        ->assertViewIs('agent.transfers.index')
        ->assertSee('Transfers')
        ->assertSee('City-to-City Rates')
        ->assertSee('Airport Rates')
        ->assertSee('Full-day Booking');
});

it('searches city transfer rates via ajax', function () {
    CityTransferRate::factory()->create([
        'from_location_id' => $this->fromLocation->id,
        'to_location_id' => $this->toLocation->id,
        'vehicle_type_id' => $this->vehicleType->id,
        'price' => 250,
        'is_active' => true,
    ]);

    $this->actingAs($this->agent)
        ->getJson(route('agent.transfers.search', [
            'tab' => 'city',
            'from_location_id' => $this->fromLocation->id,
            'to_location_id' => $this->toLocation->id,
        ]))
        ->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'tab' => 'city',
            'count' => 1,
        ]);
});

it('searches airport transfer rates via ajax', function () {
    AirportTransferRate::factory()->create([
        'airport_id' => $this->airport->id,
        'zone_id' => $this->zone->id,
        'vehicle_type_id' => $this->vehicleType->id,
        'price' => 300,
        'is_active' => true,
    ]);

    $this->actingAs($this->agent)
        ->getJson(route('agent.transfers.search', [
            'tab' => 'airport',
            'airport_id' => $this->airport->id,
        ]))
        ->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'tab' => 'airport',
            'count' => 1,
        ]);
});

it('searches full day transfer rates via ajax', function () {
    FullDayTransferRate::factory()->create([
        'from_location_id' => $this->fromLocation->id,
        'to_location_id' => $this->toLocation->id,
        'vehicle_type_id' => $this->vehicleType->id,
        'vehicle_model_id' => $this->vehicleModel->id,
        'price' => 800,
        'is_active' => true,
    ]);

    $this->actingAs($this->agent)
        ->getJson(route('agent.transfers.search', [
            'tab' => 'fullday',
            'fare_type' => 'full_day',
        ]))
        ->assertOk()
        ->assertJsonFragment([
            'success' => true,
            'tab' => 'fullday',
            'count' => 1,
        ]);
});
