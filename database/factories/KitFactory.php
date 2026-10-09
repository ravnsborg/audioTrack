<?php

namespace Database\Factories;

use App\Models\Kit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kit>
 */
class KitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kit_number' => fake()->unique()->bothify('KIT-####'),
            'status' => fake()->randomElement(array_keys(Kit::STATUSES)),
        ];
    }
}
