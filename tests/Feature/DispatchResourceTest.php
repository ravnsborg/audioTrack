<?php

use App\Filament\Resources\Dispatches\Pages\CreateDispatch;
use App\Filament\Resources\Dispatches\Pages\EditDispatch;
use App\Filament\Resources\Dispatches\Pages\ListDispatches;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\Dispatch;
use App\Models\Driver;
use App\Models\Kit;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('creates the dispatch table with the requested fields and defaults', function () {
    expect(Schema::hasColumns('dispatches', [
        'id',
        'driver_id',
        'client_location_id',
        'kit_id',
        'status',
        'queue_position',
        'token',
        'delivery_datetime',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    $dispatch = Dispatch::query()->create([
        'driver_id' => Driver::factory()->create()->getKey(),
        'client_location_id' => ClientLocation::factory()->create()->getKey(),
        'kit_id' => Kit::factory()->create()->getKey(),
    ])->refresh();

    expect($dispatch->status)->toBe('staged')
        ->and($dispatch->queue_position)->toBeInt()
        ->and(strlen($dispatch->token))->toBe(64)
        ->and($dispatch->toArray())->not->toHaveKey('token');

    $this->assertDatabaseHas(Dispatch::class, [
        'id' => $dispatch->getKey(),
        'status' => 'staged',
        'queue_position' => $dispatch->queue_position,
    ]);
});

it('lists dispatches with their driver, client location, kit, and status', function () {
    $user = User::factory()->create(['name' => 'Jordan Lee']);
    $driver = Driver::factory()->for($user)->create();
    $client = Client::factory()->create(['name' => 'Northwind Audio']);
    $location = ClientLocation::factory()->for($client)->create(['title' => 'Main Studio']);
    $kit = Kit::factory()->create(['kit_number' => 'KIT-1001']);
    $dispatch = Dispatch::factory()
        ->for($driver)
        ->for($location, 'clientLocation')
        ->for($kit)
        ->create(['status' => 'in_transit']);

    $this->actingAs(User::factory()->create())
        ->get('/admin/dispatches')
        ->assertOk()
        ->assertSee('Dispatches')
        ->assertSee('All Statuses')
        ->assertSee('Staged')
        ->assertSee('wire:model.live="tableFilters.status.value"', false)
        ->assertSee('Search', false)
        ->assertSee('Jordan Lee')
        ->assertSee('Main Studio')
        ->assertSee('KIT-1001')
        ->assertDontSee($dispatch->token);

    Livewire::test(ListDispatches::class)
        ->assertCanSeeTableRecords([$dispatch])
        ->assertTableColumnStateSet('driver.user.name', 'Jordan Lee', $dispatch)
        ->assertTableColumnStateSet('clientLocation.title', 'Main Studio', $dispatch)
        ->assertTableColumnStateSet('kit.kit_number', 'KIT-1001', $dispatch);
});

it('filters and searches dispatches on the dispatches page', function () {
    $matchingKit = Kit::factory()->create(['kit_number' => 'KIT-1234']);
    $otherKit = Kit::factory()->create(['kit_number' => 'KIT-5678']);

    $stagedDispatch = Dispatch::factory()->for($matchingKit)->create(['status' => 'staged']);
    $deliveredDispatch = Dispatch::factory()->for($otherKit)->create(['status' => 'delivered']);

    $this->actingAs(User::factory()->create());

    Livewire::test(ListDispatches::class)
        ->assertCanSeeTableRecords([$stagedDispatch, $deliveredDispatch])
        ->filterTable('status', 'delivered')
        ->assertCanSeeTableRecords([$deliveredDispatch])
        ->assertCanNotSeeTableRecords([$stagedDispatch])
        ->filterTable('status', '')
        ->searchTable('KIT-1234')
        ->assertCanSeeTableRecords([$stagedDispatch])
        ->assertCanNotSeeTableRecords([$deliveredDispatch]);
});

it('creates a dispatch with its driver, client location, and kit', function () {
    $user = User::factory()->create();
    $driver = Driver::factory()->for($user)->create();
    $client = Client::factory()->create();
    $location = ClientLocation::factory()->for($client)->create();
    $kit = Kit::factory()->create();

    $this->actingAs(User::factory()->create());

    Livewire::test(CreateDispatch::class)
        ->fillForm([
            'driver_id' => $driver->getKey(),
            'client_location_id' => $location->getKey(),
            'kit_id' => $kit->getKey(),
            'status' => 'staged',
            'queue_position' => 2,
            'delivery_datetime' => '2026-10-15 10:30:00',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    $dispatch = Dispatch::query()->sole();
    $this->assertDatabaseHas(Dispatch::class, [
        'driver_id' => $driver->getKey(),
        'client_location_id' => $location->getKey(),
        'kit_id' => $kit->getKey(),
        'status' => 'staged',
        'queue_position' => 2,
    ]);
    expect(strlen($dispatch->token))->toBe(64)
        ->and($dispatch->delivery_datetime->toDateTimeString())->toBe('2026-10-15 10:30:00')
        ->and($dispatch->driver->is($driver))->toBeTrue()
        ->and($dispatch->clientLocation->is($location))->toBeTrue()
        ->and($dispatch->kit->is($kit))->toBeTrue();
});

it('allows multiple kits to be assigned to the same client location', function () {
    $location = ClientLocation::factory()->create();
    $firstKit = Kit::factory()->create();
    $secondKit = Kit::factory()->create();

    Dispatch::factory()
        ->for($location, 'clientLocation')
        ->for($firstKit)
        ->create();
    Dispatch::factory()
        ->for($location, 'clientLocation')
        ->for($secondKit)
        ->create();

    expect($location->dispatches()->count())->toBe(2);
});

it('updates and deletes a dispatch from its admin pages', function () {
    $dispatch = Dispatch::factory()->create();
    $otherKit = Kit::factory()->create();

    $this->actingAs(User::factory()->create());

    Livewire::test(EditDispatch::class, ['record' => $dispatch->getKey()])
        ->fillForm([
            'driver_id' => $dispatch->driver_id,
            'client_location_id' => $dispatch->client_location_id,
            'kit_id' => $otherKit->getKey(),
            'status' => 'delivered',
            'queue_position' => 0,
        ])
        ->call('save')
        ->assertNotified();

    $this->assertDatabaseHas(Dispatch::class, [
        'id' => $dispatch->getKey(),
        'kit_id' => $otherKit->getKey(),
        'status' => 'delivered',
        'queue_position' => 0,
    ]);

    Livewire::test(EditDispatch::class, ['record' => $dispatch->getKey()])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    $this->assertDatabaseMissing(Dispatch::class, ['id' => $dispatch->getKey()]);
});

it('rejects dispatches without required assignments', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateDispatch::class)
        ->fillForm([
            'driver_id' => null,
            'client_location_id' => null,
            'kit_id' => null,
            'queue_position' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'driver_id' => 'required',
            'client_location_id' => 'required',
            'kit_id' => 'required',
            'queue_position' => 'required',
        ])
        ->assertNotNotified()
        ->assertNoRedirect();

    $this->assertDatabaseCount('dispatches', 0);
});

it('seeds a dispatch through the default database seeder', function () {
    $this->seed(DatabaseSeeder::class);

    $dispatch = Dispatch::query()->with(['driver', 'clientLocation', 'kit'])->sole();

    expect(strlen($dispatch->token))->toBe(64)
        ->and($dispatch->status)->toBe('staged')
        ->and($dispatch->driver)->toBeInstanceOf(Driver::class)
        ->and($dispatch->clientLocation)->toBeInstanceOf(ClientLocation::class)
        ->and($dispatch->kit)->toBeInstanceOf(Kit::class);
});
