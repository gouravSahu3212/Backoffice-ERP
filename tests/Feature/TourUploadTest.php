<?php

use App\Services\TourService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('stores uploaded itinerary pdfs and image files for a tour', function () {
    Storage::fake('public');

    $service = app(TourService::class);

    $tour = $service->create([
        'title' => 'Adventure Tour',
        'location' => 'Saudi Arabia',
        'days' => 7,
        'hotel_rating' => 4,
        'currency' => 'SAR',
        'retail_price' => 1200,
        'agent_price' => 900,
        'summary' => 'A scenic getaway',
        'description' => 'A beautiful travel experience',
        'itinerary' => 'Day 1: Arrival',
        'itinerary_pdf' => UploadedFile::fake()->create('itinerary.pdf', 150, 'application/pdf'),
        'image_urls' => [
            UploadedFile::fake()->image('cover.jpg', 1200, 800),
            UploadedFile::fake()->image('gallery.png', 1200, 800),
        ],
        'highlights' => '',
        'whats_included' => '',
        'departure_months' => '',
        'max_capacity' => 20,
        'is_active' => true,
    ]);

    $tour->refresh();

    expect($tour->itinerary_pdf)->toContain('tours/itineraries');
    expect($tour->image_urls)->toHaveCount(2)
        ->and($tour->image_urls[0])->toContain('tours/images');

    Storage::disk('public')->assertExists($tour->itinerary_pdf);
    foreach ($tour->image_urls as $imagePath) {
        Storage::disk('public')->assertExists($imagePath);
    }
});
