<?php

namespace Database\Seeders;

use App\Models\Kit;
use App\Models\KitStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KitStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusNames = [
            'available',
            'staged',
            'in_transit',
            'active',
            'delivered',
            'pending_cleaning',
        ];

        DB::transaction(function () use ($statusNames): void {
            foreach ($statusNames as $name) {
                KitStatus::query()->firstOrCreate(['name' => $name]);
            }

            $availableStatusId = KitStatus::query()
                ->where('name', 'available')
                ->value('id');
            $obsoleteStatusIds = KitStatus::query()
                ->whereNotIn('name', $statusNames)
                ->pluck('id');

            if ($obsoleteStatusIds->isNotEmpty()) {
                Kit::query()
                    ->whereIn('kit_status_id', $obsoleteStatusIds)
                    ->update(['kit_status_id' => $availableStatusId]);

                KitStatus::query()
                    ->whereKey($obsoleteStatusIds)
                    ->delete();
            }
        });
    }
}
