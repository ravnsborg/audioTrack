<?php

namespace Database\Seeders;

use App\Models\Kit;
use Illuminate\Database\Seeder;

class KitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kit::factory()
            ->count(5)
            ->create();
    }
}
