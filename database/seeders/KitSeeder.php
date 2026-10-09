<?php

namespace Database\Seeders;

use App\Models\Kit;
use App\Models\KitStatus;
use Illuminate\Database\Seeder;

class KitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(KitStatusSeeder::class);

        Kit::factory()
            ->count(5)
            ->recycle(KitStatus::query()->get())
            ->create();
    }
}
