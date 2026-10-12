<?php

use App\Filament\Resources\Items\Pages\ManageItems;
use App\Filament\Resources\Kits\Pages\ManageKits;
use App\Models\Category;
use App\Models\Driver;
use App\Models\Item;
use App\Models\Kit;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

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
        ->and(Schema::hasColumns('item_categories', ['id', 'name', 'description', 'created_at', 'updated_at']))->toBeTrue()
        ->and(Schema::hasTable('categories'))->toBeFalse()
        ->and(Schema::hasColumns('kits', ['id', 'kit_number', 'status', 'created_at', 'updated_at']))->toBeTrue()
        ->and(Schema::hasColumn('kits', 'kit_status_id'))->toBeFalse()
        ->and(Schema::hasTable('kit_statuses'))->toBeFalse();
});

it('defaults new kits to available status', function () {
    $kit = Kit::query()->create(['kit_number' => 'KIT-DEFAULT']);

    expect($kit->refresh()->status)->toBe('available');
});

it('connects items and kits to their categories', function () {
    $category = Category::factory()->create();
    $kit = Kit::factory()->create();

    $item = Item::factory()
        ->for($category)
        ->for($kit)
        ->create();

    expect($item->category->is($category))->toBeTrue()
        ->and($item->kit->is($kit))->toBeTrue()
        ->and($category->items()->whereKey($item)->exists())->toBeTrue();
});

it('seeds reusable categories without duplicate records', function () {
    $this->seed(CategorySeeder::class);
    $this->seed(CategorySeeder::class);

    expect(Category::query()->orderBy('name')->pluck('name')->all())->toBe([
        'microphone',
        'wireless receiver',
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

it('seeds sample data through the default database seeder', function () {
    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseCount('kits', 5);
    $this->assertDatabaseCount('items', 10);
    $this->assertDatabaseCount('clients', 5);
    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('drivers', 1);

    $driver = Driver::query()->with('user')->sole();
    expect($driver->user->name)->toBe('Dylan Brody')
        ->and($driver->status)->toBe('active');

    expect(Kit::query()->pluck('status')->all())
        ->each->toBeIn(array_keys(Kit::STATUSES));
    expect(Category::query()->orderBy('name')->pluck('name')->all())->toBe([
        'microphone',
        'wireless receiver',
    ]);
    expect(Item::query()->whereNull('kit_id')->exists())->toBeFalse();
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

    foreach (['/admin/items', '/admin/kits', '/admin/categories'] as $path) {
        $this->get($path)->assertOk();
    }

    $this->get('/admin/items')->assertSeeInOrder(['Dashboard', 'Items']);
    $this->get('/admin/categories')->assertSee('Item Categories');
    $this->get('/admin/kit-statuses')->assertNotFound();
    $this->get('/admin/item-statuses')->assertNotFound();
});

it('shows reference links below a divider after clients in the sidebar', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/admin/clients')
        ->assertOk()
        ->assertSeeInOrder([
            'border-top: 1px solid rgba(156, 163, 175, 0.35); margin-top: 0.5rem; padding-top: 0.5rem;',
            'Client Locations',
            'Clients',
            'border-top: 1px solid rgba(156, 163, 175, 0.35);',
            'Item Categories',
        ], false)
        ->assertDontSee('Reference data')
        ->assertDontSee('Kit statuses')
        ->assertDontSee('border-top: 1px solid rgba(156, 163, 175, 0.35); padding-top: 0.5rem;', false);
});

it('filters items by category and shows all categories by default', function () {
    $microphoneCategory = Category::factory()->create(['name' => 'microphone']);
    $receiverCategory = Category::factory()->create(['name' => 'wireless receiver']);
    $microphone = Item::factory()->for($microphoneCategory)->create();
    $receiver = Item::factory()->for($receiverCategory)->create();

    $this->actingAs(User::factory()->create());

    $this->get('/admin/items')
        ->assertOk()
        ->assertSee('wire:model.live="tableFilters.category_id.value"', false)
        ->assertSee('All Categories')
        ->assertSee('w-full sm:w-64', false)
        ->assertSeeInOrder(['tableFilters.category_id.value', 'fi-ta-search-field'], false)
        ->assertDontSee('fi-ta-filters-above-content-ctn', false);

    Livewire::test(ManageItems::class)
        ->assertCanSeeTableRecords([$microphone, $receiver])
        ->filterTable('category_id', $microphoneCategory)
        ->assertCanSeeTableRecords([$microphone])
        ->assertCanNotSeeTableRecords([$receiver]);
});

it('filters kits by status using the status dropdown', function () {
    $inTransitKit = Kit::factory()->create(['status' => 'in_transit']);
    $pendingCleaningKit = Kit::factory()->create(['status' => 'pending_cleaning']);

    $this->actingAs(User::factory()->create());

    $this->get('/admin/kits')
        ->assertOk()
        ->assertSee('Kits')
        ->assertSee('wire:model.live="tableFilters.status.value"', false)
        ->assertSee('All Statuses')
        ->assertSee('In Transit')
        ->assertSee('Pending Cleaning')
        ->assertSee('w-full sm:w-64', false)
        ->assertSeeInOrder(['tableFilters.status.value', 'fi-ta-search-field'], false)
        ->assertDontSee('fi-ta-filters-above-content-ctn', false)
        ->assertDontSee('kit_status_id');

    Livewire::test(ManageKits::class)
        ->assertCanSeeTableRecords([$inTransitKit, $pendingCleaningKit])
        ->filterTable('status', 'pending_cleaning')
        ->assertCanSeeTableRecords([$pendingCleaningKit])
        ->assertCanNotSeeTableRecords([$inTransitKit]);
});

it('shows a kit item list in the items column hover tooltip', function () {
    $category = Category::factory()->create();
    $kit = Kit::factory()->create();
    Item::factory()->for($category)->for($kit)->create([
        'name' => 'Wireless microphone receiver',
        'sku' => 'SKU-HOVER-001',
    ]);
    Item::factory()->for($category)->for($kit)->create([
        'name' => 'Handheld microphone',
        'sku' => 'SKU-HOVER-002',
    ]);

    $this->actingAs(User::factory()->create())
        ->get('/admin/kits')
        ->assertOk()
        ->assertSee('Wireless microphone receiver')
        ->assertSee('SKU-HOVER-001')
        ->assertSee('Handheld microphone')
        ->assertSee('SKU-HOVER-002')
        ->assertSee('\u003Ch3 style=\u0022font-weight: 600; margin: 0;\u0022\u003EAssigned Items\u003C\/h3', false)
        ->assertSee('\u003Chr style=\u0022border: 0; border-top: 1px solid currentColor; margin: 0.5rem 0;\u0022\u003E', false)
        ->assertSee('\u003Cli\u003E\u0026bull; Wireless microphone receiver', false)
        ->assertSee('\u003Cli\u003E\u0026bull; Handheld microphone', false);
});
