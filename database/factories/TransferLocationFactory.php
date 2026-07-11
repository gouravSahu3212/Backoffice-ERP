<?php

namespace Database\Factories;

use App\Models\TransferLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferLocation>
 */
class TransferLocationFactory extends Factory
{
    protected $model = TransferLocation::class;

    public function definition(): array
    {
        return [
            'name'      => $this->faker->city(),
            'type'      => 'city',
            'is_active' => true,
        ];
    }

    public function airport(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'airport',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
