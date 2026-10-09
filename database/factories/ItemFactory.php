<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kit_id' => null,
            'sku' => fake()->unique()->bothify('SKU-####'),
            'serial_number' => fake()->unique()->optional()->bothify('SN-########'),
            'name' => fake()->words(3, true),
            'category_id' => Category::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
