<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Dylan Brody',
        ]);

        Driver::factory()
            ->for($user)
            ->create([
                'status' => 'active',
            ]);
    }
}
