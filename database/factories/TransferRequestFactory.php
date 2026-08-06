<?php

namespace Database\Factories;

use App\Models\TransferRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TransferRequest>
 */
class TransferRequestFactory extends Factory
{
    protected $model = TransferRequest::class;

    public function definition(): array
    {
        return [
            'request_reference' => 'TRF-'.strtolower(Str::random(8)),
            'agent_id' => User::factory(),
            'transfer_type_category' => 'city',
            'title' => 'Economy Sedan',
            'route_label' => 'Dubai → Abu Dhabi',
            'vehicle' => 'Sedan',
            'customer_name' => fake()->name(),
            'date_of_birth' => fake()->date(),
            'passport_number' => strtoupper(fake()->bothify('??######')),
            'total_price' => 250.00,
            'currency' => 'AED',
            'status' => 'new',
        ];
    }
}
