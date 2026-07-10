<?php

use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

it('stores itinerary pdf and multiple image uploads through admin tour create', function () {
    Storage::fake('public');

    Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->assignRole('Super Admin');

    $response = $this->actingAs($user)
        ->post(route('admin.tours.store'), [
            'title' => 'Test Tour',
            'location' => 'Saudi Arabia',
            'days' => 5,
            'hotel_rating' => 4,
            'currency' => 'SAR',
            'retail_price' => 1500,
            'agent_price' => 1200,
            'summary' => 'Test summary',
            'description' => 'Test description',
            'itinerary' => 'Day 1: Test',
            'itinerary_pdf' => UploadedFile::fake()->create('itinerary.pdf', 100, 'application/pdf'),
            'image_urls' => [
                UploadedFile::fake()->image('img1.jpg'),
                UploadedFile::fake()->image('img2.png'),
            ],
            'highlights' => "Feature 1\nFeature 2",
            'whats_included' => "Item 1\nItem 2",
            'departure_months' => [
                ['date' => '2026-10-16', 'slots' => 10],
                ['date' => '2026-10-30', 'slots' => 10],
            ],
            'max_capacity' => 20,
            'is_active' => 1,
        ]);

    $response->assertRedirect(route('admin.tours.index'));

    $tour = Tour::where('title', 'Test Tour')->first();
    expect($tour)->not->toBeNull();
    expect($tour->itinerary_pdf)->not->toBeNull();
    expect($tour->image_urls)->toHaveCount(2);

    Storage::disk('public')->assertExists($tour->itinerary_pdf);
    foreach ($tour->image_urls as $path) {
        Storage::disk('public')->assertExists($path);
    }
});
