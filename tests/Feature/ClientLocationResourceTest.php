<?php

use App\Filament\Resources\ClientLocations\Pages\CreateClientLocation;
use App\Filament\Resources\ClientLocations\Pages\EditClientLocation;
use App\Filament\Resources\ClientLocations\Pages\ListClientLocations;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\User;
use Database\Seeders\ClientLocationSeeder;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('lists client locations and stores the address as text', function () {
    $client = Client::factory()->create(['name' => 'Northwind Audio']);
    $location = ClientLocation::factory()->for($client)->create([
        'title' => 'Main Studio',
        'address' => "14 Sound Street\nPortland, OR",
    ]);

    $this->actingAs(User::factory()->create())
        ->get('/admin/client-locations')
        ->assertOk()
        ->assertSee('Client Locations')
        ->assertSee('Main Studio')
        ->assertSee('Northwind Audio');

    Livewire::test(ListClientLocations::class)
        ->assertCanSeeTableRecords([$location]);

    expect(Schema::hasColumns('client_locations', [
        'id',
        'client_id',
        'title',
        'address',
        'created_at',
        'updated_at',
    ]))->toBeTrue()
        ->and(Schema::getColumnType('client_locations', 'address'))->toBe('text');
});

it('filters client locations by client using the kits dropdown style', function () {
    $northwind = Client::factory()->create(['name' => 'Northwind Audio']);
    $southwind = Client::factory()->create(['name' => 'Southwind Audio']);
    $northwindLocation = ClientLocation::factory()->for($northwind)->create();
    $southwindLocation = ClientLocation::factory()->for($southwind)->create();

    $this->actingAs(User::factory()->create());

    $this->get('/admin/client-locations')
        ->assertOk()
        ->assertSee('wire:model.live="tableFilters.client_id.value"', false)
        ->assertSee('All Clients')
        ->assertSee('w-full sm:w-64', false)
        ->assertSeeInOrder(['tableFilters.client_id.value', 'fi-ta-search-field'], false)
        ->assertDontSee('fi-ta-filters-above-content-ctn', false);

    Livewire::test(ListClientLocations::class)
        ->assertCanSeeTableRecords([$northwindLocation, $southwindLocation])
        ->filterTable('client_id', $northwind)
        ->assertCanSeeTableRecords([$northwindLocation])
        ->assertCanNotSeeTableRecords([$southwindLocation]);
});

it('creates a client location from the Filament form', function () {
    $client = Client::factory()->create();

    $this->actingAs(User::factory()->create());

    $locationData = [
        'client_id' => $client->getKey(),
        'title' => 'Main Studio',
        'address' => "14 Sound Street\nPortland, OR",
    ];

    Livewire::test(CreateClientLocation::class)
        ->fillForm($locationData)
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    $this->assertDatabaseHas(ClientLocation::class, $locationData);
});

it('requires a client, title, and address for a client location', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateClientLocation::class)
        ->fillForm([
            'client_id' => null,
            'title' => null,
            'address' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'client_id' => 'required',
            'title' => 'required',
            'address' => 'required',
        ])
        ->assertNotNotified()
        ->assertNoRedirect();

    $this->assertDatabaseCount('client_locations', 0);
});

it('updates a client location from the Filament edit page', function () {
    $client = Client::factory()->create();
    $location = ClientLocation::factory()->for($client)->create();

    $this->actingAs(User::factory()->create());

    Livewire::test(EditClientLocation::class, ['record' => $location->getKey()])
        ->fillForm([
            'client_id' => $client->getKey(),
            'title' => 'Updated Studio',
            'address' => "100 Main Street\nSan Francisco, CA",
        ])
        ->call('save')
        ->assertNotified();

    $this->assertDatabaseHas(ClientLocation::class, [
        'id' => $location->getKey(),
        'client_id' => $client->getKey(),
        'title' => 'Updated Studio',
        'address' => "100 Main Street\nSan Francisco, CA",
    ]);
});

it('deletes a client location from the Filament edit page', function () {
    $client = Client::factory()->create();
    $location = ClientLocation::factory()->for($client)->create();

    $this->actingAs(User::factory()->create());

    Livewire::test(EditClientLocation::class, ['record' => $location->getKey()])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    $this->assertDatabaseMissing(ClientLocation::class, ['id' => $location->getKey()]);
});

it('seeds two locations for each existing client', function () {
    $clients = Client::factory()->count(2)->create();

    $this->seed(ClientLocationSeeder::class);

    $this->assertDatabaseCount('client_locations', 4);

    foreach ($clients as $client) {
        expect($client->locations()->count())->toBe(2);
    }
});
