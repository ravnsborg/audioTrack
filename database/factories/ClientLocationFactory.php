<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\ClientLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientLocation>
 */
class ClientLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'title' => fake()->words(3, true),
            'address' => fake()->address(),
        ];
    }
}
