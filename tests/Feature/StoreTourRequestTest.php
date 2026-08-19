<?php

use App\Models\Tour;
use App\Models\TourRequest;
use App\Models\User;
use App\Notifications\BookingRequestStatusUpdatedNotification;
use App\Notifications\NewBookingRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure roles exist
    Role::firstOrCreate(['name' => 'Agent', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Super Admin');
});

function makeAgentWithTour(): array
{
    $agent = User::factory()->create();
    $agent->assignRole('Agent');

    $tour = Tour::factory()->create([
        'agent_price' => 1000,
        'currency' => 'USD',
        'max_capacity' => 20,
        'departure_months' => [
            ['date' => '2027-03-15', 'slots' => 10],
        ],
    ]);

    return compact('agent', 'tour');
}

function validPayload(): array
{
    return [
        'customer_name' => 'John Doe',
        'date_of_birth' => '1990-05-20',
        'passport_number' => 'AB1234567',
        'departure_date' => '2027-03-15',
        'pax' => 2,
        'total_price' => 2000,
        'currency' => 'USD',
    ];
}

test('agent can submit a tour enquiry', function () {
    Notification::fake();

    ['agent' => $agent, 'tour' => $tour] = makeAgentWithTour();

    $response = $this->actingAs($agent)
        ->postJson(route('agent.tours.enquire', $tour), validPayload());

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['success', 'message', 'reference']);

    Notification::assertSentTo($this->admin, NewBookingRequestNotification::class);

    expect(TourRequest::count())->toBe(1);

    $req = TourRequest::first();
    expect($req->customer_name)->toBe('John Doe')
        ->and($req->passport_number)->toBe('AB1234567')
        ->and($req->pax)->toBe(2)
        ->and((float) $req->total_price)->toBe(2000.0)
        ->and($req->agent_id)->toBe($agent->id)
        ->and($req->tour_id)->toBe($tour->id)
        ->and($req->status)->toBe('new');
});

test('enquiry submission fails with empty required fields', function () {
    ['agent' => $agent, 'tour' => $tour] = makeAgentWithTour();

    $response = $this->actingAs($agent)
        ->postJson(route('agent.tours.enquire', $tour), []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['customer_name', 'date_of_birth', 'passport_number', 'departure_date', 'pax', 'total_price', 'currency']);
});

test('enquiry fails when customer name is missing', function () {
    ['agent' => $agent, 'tour' => $tour] = makeAgentWithTour();

    $payload = validPayload();
    unset($payload['customer_name']);

    $this->actingAs($agent)
        ->postJson(route('agent.tours.enquire', $tour), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['customer_name']);
});

test('admin can view all tour requests', function () {
    ['agent' => $agent, 'tour' => $tour] = makeAgentWithTour();

    TourRequest::factory()->count(3)->create([
        'tour_id' => $tour->id,
        'agent_id' => $agent->id,
    ]);

    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $this->actingAs($admin)
        ->get(route('admin.tour-requests.index'))
        ->assertOk()
        ->assertViewIs('admin.tours.requests.index');
});

test('admin can update tour request status', function () {
    Notification::fake();

    ['agent' => $agent, 'tour' => $tour] = makeAgentWithTour();

    $req = TourRequest::factory()->create([
        'tour_id' => $tour->id,
        'agent_id' => $agent->id,
        'status' => 'new',
    ]);

    $admin = User::factory()->create();
    $admin->assignRole('Super Admin');

    $this->actingAs($admin)
        ->patchJson(route('admin.tour-requests.update-status', $req), ['status' => 'confirmed'])
        ->assertOk()
        ->assertJsonPath('success', true);

    Notification::assertSentTo($agent, BookingRequestStatusUpdatedNotification::class);

    expect($req->fresh()->status)->toBe('confirmed');
});

test('agent can view their own enquiries', function () {
    ['agent' => $agent, 'tour' => $tour] = makeAgentWithTour();

    TourRequest::factory()->count(2)->create([
        'tour_id' => $tour->id,
        'agent_id' => $agent->id,
    ]);

    $this->actingAs($agent)
        ->get(route('agent.tour-requests.index'))
        ->assertOk()
        ->assertViewIs('agent.tours.requests.index');
});
