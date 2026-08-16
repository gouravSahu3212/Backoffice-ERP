<?php

use App\Models\CityTransferRate;
use App\Models\TransferLocation;
use App\Models\TransferRequest;
use App\Models\User;
use App\Models\VehicleType;
use App\Notifications\BookingRequestStatusUpdatedNotification;
use App\Notifications\NewBookingRequestNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

    $this->agent = User::factory()->create();
    $this->agent->assignRole('Agent');

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');

    $this->fromLocation = TransferLocation::factory()->create(['name' => 'Dubai', 'type' => 'city']);
    $this->toLocation = TransferLocation::factory()->create(['name' => 'Abu Dhabi', 'type' => 'city']);
    $this->vehicleType = VehicleType::factory()->create(['name' => 'Sedan']);

    $this->rate = CityTransferRate::factory()->create([
        'from_location_id' => $this->fromLocation->id,
        'to_location_id' => $this->toLocation->id,
        'vehicle_type_id' => $this->vehicleType->id,
        'price' => 250,
    ]);
});

it('restricts guests from accessing transfer request routes', function () {
    $this->get(route('agent.transfer-requests.index'))
        ->assertRedirect(route('login'));

    $this->get(route('admin.transfer-requests.index'))
        ->assertRedirect(route('login'));
});

it('allows agent to submit a transfer booking enquiry', function () {
    Notification::fake();

    $payload = [
        'customer_name' => 'John Doe',
        'date_of_birth' => '1990-05-15',
        'passport_number' => 'A1234567',
        'pickup_date' => '2026-08-20',
        'pickup_time' => '14:30',
        'title' => 'Economy Sedan',
        'route_label' => 'Dubai → Abu Dhabi',
        'vehicle' => 'Sedan',
        'transfer_type_category' => 'city',
        'rate_id' => $this->rate->id,
        'total_price' => 250.00,
        'currency' => 'AED',
    ];

    $response = $this->actingAs($this->agent)
        ->postJson(route('agent.transfers.enquire'), $payload)
        ->assertOk()
        ->assertJsonFragment(['success' => true]);

    Notification::assertSentTo($this->admin, NewBookingRequestNotification::class);

    $reference = $response->json('reference');

    $this->assertDatabaseHas('transfer_requests', [
        'request_reference' => $reference,
        'agent_id' => $this->agent->id,
        'customer_name' => 'John Doe',
        'passport_number' => 'A1234567',
        'pickup_date' => '2026-08-20 00:00:00',
        'pickup_time' => '14:30',
        'status' => 'new',
    ]);

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->agent->id,
        'action' => 'created',
        'related_agent_id' => $this->agent->id,
    ]);
});

it('allows agent to view own transfer booking enquiries', function () {
    $request1 = TransferRequest::factory()->create([
        'agent_id' => $this->agent->id,
        'customer_name' => 'Agent Customer',
    ]);

    $otherAgent = User::factory()->create();
    $otherAgent->assignRole('Agent');
    $request2 = TransferRequest::factory()->create([
        'agent_id' => $otherAgent->id,
        'customer_name' => 'Other Customer',
    ]);

    $this->actingAs($this->agent)
        ->get(route('agent.transfer-requests.index'))
        ->assertOk()
        ->assertSee('Agent Customer')
        ->assertDontSee('Other Customer');
});

it('allows admin to view all transfer booking enquiries', function () {
    $request1 = TransferRequest::factory()->create(['customer_name' => 'Customer Alpha']);
    $request2 = TransferRequest::factory()->create(['customer_name' => 'Customer Beta']);

    $this->actingAs($this->admin)
        ->get(route('admin.transfer-requests.index'))
        ->assertOk()
        ->assertSee('Customer Alpha')
        ->assertSee('Customer Beta');
});

it('allows admin to update transfer request status and logs activity', function () {
    Notification::fake();

    $transferRequest = TransferRequest::factory()->create([
        'agent_id' => $this->agent->id,
        'status' => 'new',
    ]);

    $this->actingAs($this->admin)
        ->patchJson(route('admin.transfer-requests.update-status', $transferRequest), [
            'status' => 'confirmed',
        ])
        ->assertOk()
        ->assertJson(['success' => true]);

    Notification::assertSentTo($this->agent, BookingRequestStatusUpdatedNotification::class);

    $this->assertDatabaseHas('transfer_requests', [
        'id' => $transferRequest->id,
        'status' => 'confirmed',
    ]);

    $this->assertDatabaseHas('activity_logs', [
        'user_id' => $this->admin->id,
        'action' => 'updated',
        'related_agent_id' => $this->agent->id,
    ]);
});
