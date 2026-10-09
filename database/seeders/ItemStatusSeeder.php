<?php

namespace Database\Seeders;

use App\Models\ItemStatus;
use Illuminate\Database\Seeder;

class ItemStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Available' => 'Available for assignment.',
            'In Use' => 'Currently assigned to an active kit or operation.',
            'Maintenance' => 'Temporarily unavailable for maintenance.',
            'Retired' => 'No longer in active inventory.',
        ] as $name => $description) {
            ItemStatus::query()->firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }
    }
}
