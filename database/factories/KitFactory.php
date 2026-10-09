<?php

namespace Database\Factories;

use App\Models\Kit;
use App\Models\KitStatus;
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
            'kit_status_id' => KitStatus::factory(),
        ];
    }
}
