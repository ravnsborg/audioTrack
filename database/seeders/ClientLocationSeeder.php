<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\ClientLocation;
use Illuminate\Database\Seeder;

class ClientLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::query()->each(function (Client $client): void {
            ClientLocation::factory()
                ->count(2)
                ->for($client)
                ->create();
        });
    }
}
