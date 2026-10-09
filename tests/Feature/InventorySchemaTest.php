<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\Kit;
use App\Models\KitStatus;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\KitStatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('creates the requested inventory tables and columns', function () {
    expect(Schema::hasColumns('items', [
        'id',
        'kit_id',
        'sku',
        'serial_number',
        'name',
        'category_id',
        'notes',
        'created_at',
        'updated_at',
    ]))->toBeTrue()
        ->and(Schema::hasColumn('items', 'items_status_id'))->toBeFalse()
        ->and(Schema::hasTable('item_statuses'))->toBeFalse()
        ->and(Schema::hasColumns('categories', ['id', 'name', 'description', 'created_at', 'updated_at']))->toBeTrue()
        ->and(Schema::hasColumns('kits', ['id', 'kit_number', 'kit_status_id', 'created_at', 'updated_at']))->toBeTrue()
        ->and(Schema::hasColumns('kit_statuses', ['id', 'name', 'description', 'created_at', 'updated_at']))->toBeTrue();
});

it('connects items and kits to their categories and statuses', function () {
    $category = Category::factory()->create();
    $kit = Kit::factory()->create();
    $kitStatus = $kit->kitStatus;

    $item = Item::factory()
        ->for($category)
        ->for($kit)
        ->create();

    expect($item->category->is($category))->toBeTrue()
        ->and($item->kit->is($kit))->toBeTrue()
        ->and($kit->kitStatus->is($kitStatus))->toBeTrue()
        ->and($category->items()->whereKey($item)->exists())->toBeTrue();
});

it('seeds reusable categories and statuses without duplicate records', function () {
    $this->seed([
        CategorySeeder::class,
        KitStatusSeeder::class,
    ]);
    $this->seed([
        CategorySeeder::class,
        KitStatusSeeder::class,
    ]);

    expect(Category::query()->orderBy('name')->pluck('name')->all())->toBe([
        'microphone',
        'wireless receiver',
    ])
        ->and(KitStatus::query()->orderBy('name')->pluck('name')->all())->toBe([
            'active',
            'available',
            'delivered',
            'in_transit',
            'pending_cleaning',
            'staged',
        ]);
});

it('removes unrecognized categories and reassigns their items when reseeded', function () {
    $obsoleteCategory = Category::factory()->create();
    $item = Item::factory()->for($obsoleteCategory)->create();

    $this->seed(CategorySeeder::class);

    $item->refresh();
    expect(Category::query()->orderBy('name')->pluck('name')->all())->toBe([
        'microphone',
        'wireless receiver',
    ]);
    expect($item->category->name)->toBe('microphone');
});

it('seeds sample items and kits through the default database seeder', function () {
    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('kits', 5);
    $this->assertDatabaseCount('items', 10);
    expect(Category::query()->orderBy('name')->pluck('name')->all())->toBe([
        'microphone',
        'wireless receiver',
    ]);
    expect(KitStatus::query()->orderBy('name')->pluck('name')->all())->toBe([
        'active',
        'available',
        'delivered',
        'in_transit',
        'pending_cleaning',
        'staged',
    ]);
    expect(Item::query()->whereNull('kit_id')->exists())->toBeFalse();
});

it('removes unrecognized kit statuses and reassigns their kits when reseeded', function () {
    $obsoleteKitStatus = KitStatus::factory()->create();
    $kit = Kit::factory()->for($obsoleteKitStatus, 'kitStatus')->create();

    $this->seed(KitStatusSeeder::class);

    $kit->refresh();
    expect(KitStatus::query()->orderBy('name')->pluck('name')->all())->toBe([
        'active',
        'available',
        'delivered',
        'in_transit',
        'pending_cleaning',
        'staged',
    ]);
    expect($kit->kitStatus->name)->toBe('available');
});

it('assigns kits to existing items without one when the database is reseeded', function () {
    $existingItem = Item::factory()->create(['kit_id' => null]);

    $this->seed(DatabaseSeeder::class);

    $existingItem->refresh();
    expect($existingItem->kit_id)->not->toBeNull()
        ->and(Item::query()->whereNull('kit_id')->exists())->toBeFalse();
});

it('shows inventory navigation after the dashboard for authenticated users', function () {
    $this->actingAs(User::factory()->create());

    foreach (['/admin/items', '/admin/kits', '/admin/categories', '/admin/kit-statuses'] as $path) {
        $this->get($path)->assertOk();
    }

    $this->get('/admin/items')->assertSeeInOrder(['Dashboard', 'Inventory']);
    $this->get('/admin/item-statuses')->assertNotFound();
});
