<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    private static array $locations = [
        'Saudi Arabia',
        'United Arab Emirates',
        'Egypt',
        'Jordan',
        'Morocco',
        'Turkey',
        'Oman',
        'Bahrain',
    ];

    private static array $months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $retailPrice = fake()->numberBetween(1500, 6000);
        $agentPrice = (int) ($retailPrice * fake()->randomFloat(2, 0.75, 0.90));

        return [
            'title' => fake()->sentence(6),
            'location' => fake()->randomElement(self::$locations),
            'days' => fake()->numberBetween(3, 15),
            'hotel_rating' => fake()->numberBetween(3, 5),
            'currency' => 'USD',
            'retail_price' => $retailPrice,
            'agent_price' => $agentPrice,
            'summary' => fake()->paragraph(),
            'description' => fake()->paragraphs(3, true),
            'itinerary' => "Day 1: Arrival\nDay 2: City Tour\nDay 3: Desert Safari",
            'highlights' => [
                'Professional English-speaking guide',
                'All breakfasts included',
                'Luxury hotel accommodation',
            ],
            'whats_included' => [
                'Airport transfers',
                'Accommodation',
                'Daily breakfast',
            ],
            'image_urls' => [],
            'departure_months' => fake()->randomElements(self::$months, fake()->numberBetween(2, 4)),
            'max_capacity' => 20,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['is_active' => false]);
    }
}
