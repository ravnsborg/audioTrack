<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryNames = [
            'wireless receiver',
            'microphone',
        ];

        DB::transaction(function () use ($categoryNames): void {
            foreach ($categoryNames as $name) {
                Category::query()->firstOrCreate(['name' => $name]);
            }

            $microphoneCategoryId = Category::query()
                ->where('name', 'microphone')
                ->value('id');
            $obsoleteCategoryIds = Category::query()
                ->whereNotIn('name', $categoryNames)
                ->pluck('id');

            if ($obsoleteCategoryIds->isNotEmpty()) {
                Item::query()
                    ->whereIn('category_id', $obsoleteCategoryIds)
                    ->update(['category_id' => $microphoneCategoryId]);

                Category::query()
                    ->whereKey($obsoleteCategoryIds)
                    ->delete();
            }
        });
    }
}
