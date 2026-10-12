<?php

use App\Filament\Widgets\TodaysDispatches;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\Dispatch;
use App\Models\Driver;
use App\Models\Kit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('shows today’s dispatch details and filters out dispatches scheduled for other days', function () {
    $this->travelTo(now()->startOfDay()->addHours(9));

    $client = Client::factory()->create(['name' => 'Northwind Audio']);
    $location = ClientLocation::factory()->for($client)->create(['title' => 'Main Studio']);
    $driver = Driver::factory()->for(User::factory()->create(['name' => 'Jordan Lee']))->create();
    $kit = Kit::factory()->create(['kit_number' => 'KIT-1001']);

    Dispatch::factory()
        ->for($driver)
        ->for($location, 'clientLocation')
        ->for($kit)
        ->create([
            'status' => 'in_transit',
            'queue_position' => 3,
            'delivery_datetime' => today()->addHours(10),
        ]);

    $tomorrowLocation = ClientLocation::factory()->create(['title' => 'Tomorrow Studio']);
    Dispatch::factory()->for($tomorrowLocation, 'clientLocation')->create([
        'delivery_datetime' => today()->addDay()->addHours(10),
    ]);

    Dispatch::factory()->create(['delivery_datetime' => null]);

    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSeeInOrder(['Total Kits', 'Pending Cleaning', 'Today’s Dispatches'])
        ->assertSee('wire:model.live="tableRecordsPerPage"', false)
        ->assertSee('Per page')
        ->assertSee('.dispatches-table-per-page .fi-pagination-records-per-page-select-ctn', false)
        ->assertDontSee('wire:model.live="tableFilters.status.value"', false)
        ->assertSee('wire:model.live="tableFilters.driver_id.value"', false)
        ->assertSee('All Drivers')
        ->assertSee('Queue Position')
        ->assertSee('Client Location')
        ->assertSee('Main Studio')
        ->assertSee('Northwind Audio')
        ->assertSee('KIT-1001')
        ->assertSee('Jordan Lee')
        ->assertSee('In Transit')
        ->assertSee('3')
        ->assertDontSee('Tomorrow Studio');
});

it('changes the number of today’s dispatches shown per page', function () {
    $this->travelTo(now()->startOfDay()->addHours(9));

    $dispatches = collect(range(1, 6))
        ->map(fn (int $queuePosition): Dispatch => Dispatch::factory()->create([
            'queue_position' => $queuePosition,
            'delivery_datetime' => today()->addHours(10),
        ]));

    $this->actingAs(User::factory()->create());

    Livewire::test(TodaysDispatches::class)
        ->set('tableRecordsPerPage', 5)
        ->assertCanSeeTableRecords($dispatches->take(5))
        ->assertCanNotSeeTableRecords([$dispatches->last()])
        ->set('tableRecordsPerPage', 10)
        ->assertCanSeeTableRecords($dispatches);
});

it('filters today’s dispatches by driver', function () {
    $this->travelTo(now()->startOfDay()->addHours(9));

    $firstDriver = Driver::factory()
        ->for(User::factory()->create(['name' => 'Jordan Lee']))
        ->create();
    $secondDriver = Driver::factory()
        ->for(User::factory()->create(['name' => 'Taylor Morgan']))
        ->create();

    $firstDriversDispatch = Dispatch::factory()->for($firstDriver)->create([
        'delivery_datetime' => today()->addHours(10),
    ]);
    $secondDriversDispatch = Dispatch::factory()->for($secondDriver)->create([
        'delivery_datetime' => today()->addHours(11),
    ]);
    $tomorrowsDispatch = Dispatch::factory()->for($firstDriver)->create([
        'delivery_datetime' => today()->addDay()->addHours(10),
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test(TodaysDispatches::class)
        ->assertCanSeeTableRecords([$firstDriversDispatch, $secondDriversDispatch])
        ->assertCanNotSeeTableRecords([$tomorrowsDispatch])
        ->filterTable('driver_id', $firstDriver->getKey())
        ->assertCanSeeTableRecords([$firstDriversDispatch])
        ->assertCanNotSeeTableRecords([$secondDriversDispatch, $tomorrowsDispatch]);
});
