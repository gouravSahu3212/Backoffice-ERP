<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\HotelRoomSlot;
use App\Models\TransferLocation;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $defaultAmenities = [
            'WiFi',
            'Pool',
            'Spa',
            'Gym',
            'Restaurant',
            'Bar',
            'Parking',
            'Room Service',
            'Beach Access',
            'Airport Shuttle',
            'Business Center',
            'Laundry',
        ];

        foreach ($defaultAmenities as $amenityName) {
            Amenity::firstOrCreate(
                ['name' => $amenityName],
                ['is_active' => true]
            );
        }

        $dubai = TransferLocation::where('name', 'Dubai')->first()
            ?? TransferLocation::create(['name' => 'Dubai', 'type' => 'city', 'is_active' => true]);

        $abuDhabi = TransferLocation::where('name', 'Abu Dhabi')->first()
            ?? TransferLocation::create(['name' => 'Abu Dhabi', 'type' => 'city', 'is_active' => true]);

        $sharjah = TransferLocation::where('name', 'Sharjah')->first()
            ?? TransferLocation::create(['name' => 'Sharjah', 'type' => 'city', 'is_active' => true]);

        $sampleHotels = [
            [
                'name' => 'Marina Grand Resort',
                'location_id' => $dubai->id,
                'address' => 'Dubai Marina Waterfront, Dubai, UAE',
                'description' => 'Luxury 5-star resort located at the heart of Dubai Marina offering panoramic ocean views and private beach access.',
                'terms_and_conditions' => 'Check-in from 15:00. Check-out before 12:00. Free cancellation up to 48 hours prior to arrival date.',
                'star_rating' => 5,
                'amenities' => ['WiFi', 'Pool', 'Spa', 'Gym', 'Restaurant', 'Bar', 'Beach Access'],
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Downtown Business Hotel',
                'location_id' => $dubai->id,
                'address' => 'Downtown Boulevard, Near Burj Khalifa, Dubai, UAE',
                'description' => 'Modern business hotel in the heart of Downtown Dubai.',
                'terms_and_conditions' => 'Check-in 14:00. Check-out 11:00. Non-refundable rate policy applies for peak season bookings.',
                'star_rating' => 4,
                'amenities' => ['WiFi', 'Gym', 'Restaurant', 'Business Center', 'Parking', 'Room Service'],
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Abu Dhabi Palace Hotel',
                'location_id' => $abuDhabi->id,
                'address' => 'Corniche Road West, Abu Dhabi, UAE',
                'description' => 'Opulent 5-star palace hotel offering world-class dining, private beaches, and regal hospitality.',
                'terms_and_conditions' => 'Check-in 15:00. Passport required at check-in. Children under 12 stay free.',
                'star_rating' => 5,
                'amenities' => ['WiFi', 'Pool', 'Spa', 'Gym', 'Restaurant', 'Bar', 'Parking', 'Room Service', 'Beach Access', 'Airport Shuttle'],
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Sharjah Heritage Inn',
                'location_id' => $sharjah->id,
                'address' => 'Heart of Sharjah Cultural District, Sharjah, UAE',
                'description' => 'Boutique heritage stay preserving traditional Emirati architecture with modern luxury amenities.',
                'terms_and_conditions' => 'Check-in 14:00. Check-out 12:00. Strict non-smoking policy throughout the property.',
                'star_rating' => 3,
                'amenities' => ['WiFi', 'Restaurant', 'Room Service', 'Laundry'],
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'JBR Beach Hotel',
                'location_id' => $dubai->id,
                'address' => 'Jumeirah Beach Residence, Dubai, UAE',
                'description' => 'Vibrant 4-star beachfront hotel with direct access to The Walk at JBR, infinity pools, and water sports.',
                'terms_and_conditions' => 'Check-in 15:00. Security deposit required upon check-in. Pets not permitted.',
                'star_rating' => 4,
                'amenities' => ['WiFi', 'Pool', 'Gym', 'Restaurant', 'Bar', 'Beach Access', 'Airport Shuttle'],
                'is_active' => true,
                'is_featured' => true,
            ],
        ];

        foreach ($sampleHotels as $hotelData) {
            $hotel = Hotel::firstOrCreate(
                ['name' => $hotelData['name'], 'location_id' => $hotelData['location_id']],
                $hotelData
            );

            // Seed sample slots for each hotel if none exist
            if ($hotel->slots()->count() === 0) {
                HotelRoomSlot::create([
                    'hotel_id' => $hotel->id,
                    'name' => 'Standard Room',
                    'capacity' => 2,
                    'available_qty' => 20,
                    'price_per_night' => 280,
                    'currency' => 'AED',
                    'is_active' => true,
                ]);

                HotelRoomSlot::create([
                    'hotel_id' => $hotel->id,
                    'name' => 'Executive Room',
                    'capacity' => 2,
                    'available_qty' => 8,
                    'price_per_night' => 420,
                    'currency' => 'AED',
                    'is_active' => true,
                ]);
            }
        }
    }
}
