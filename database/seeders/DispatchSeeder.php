<?php

namespace Database\Seeders;

use App\Models\ClientLocation;
use App\Models\Dispatch;
use App\Models\Driver;
use App\Models\Kit;
use Illuminate\Database\Seeder;

class DispatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $driver = Driver::query()->firstOrFail();
        $clientLocation = ClientLocation::query()->firstOrFail();
        $kit = Kit::query()->firstOrFail();

        Dispatch::factory()
            ->for($driver)
            ->for($clientLocation, 'clientLocation')
            ->for($kit)
            ->create();
    }
}
