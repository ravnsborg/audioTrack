<?php

namespace Database\Factories;

use App\Models\ClientLocation;
use App\Models\Dispatch;
use App\Models\Driver;
use App\Models\Kit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Dispatch>
 */
class DispatchFactory extends Factory
{
    public function configure(): static
    {
        return $this->afterMaking(function (Dispatch $dispatch): void {
            if (blank($dispatch->token)) {
                $dispatch->forceFill([
                    'token' => Str::random(64),
                ]);
            }
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory(),
            'client_location_id' => ClientLocation::factory(),
            'kit_id' => Kit::factory(),
            'status' => 'staged',
            'queue_position' => fake()->numberBetween(0, 10),
            'delivery_datetime' => null,
        ];
    }
}
