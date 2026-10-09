<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\Kit;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        $kits = Kit::query()->get();

        if ($kits->isEmpty()) {
            $kits = Kit::factory()->count(5)->create();
        }

        Item::query()
            ->whereNull('kit_id')
            ->update(['kit_id' => $kits->random()->getKey()]);

        Item::factory()
            ->count(10)
            ->recycle(Category::query()->get())
            ->state(fn (): array => ['kit_id' => $kits->random()->getKey()])
            ->create();
    }
}
