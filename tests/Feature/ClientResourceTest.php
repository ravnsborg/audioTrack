<?php

use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('shows client records in Filament and stores addresses as text', function () {
    $client = Client::factory()->create([
        'name' => 'Northwind Audio',
        'address' => "14 Sound Street\nPortland, OR",
    ]);

    $this->actingAs(User::factory()->create())
        ->get('/admin/clients')
        ->assertOk()
        ->assertSee('Clients')
        ->assertSee('Northwind Audio');

    Livewire::test(ListClients::class)
        ->assertCanSeeTableRecords([$client]);

    expect(Schema::hasColumns('clients', [
        'id',
        'name',
        'primary_contact_name',
        'phone',
        'email',
        'address',
        'created_at',
        'updated_at',
    ]))->toBeTrue()
        ->and(Schema::getColumnType('clients', 'address'))->toBe('text');
});

it('does not show breadcrumbs at the top of the clients page', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/clients')
        ->assertOk()
        ->assertDontSee('fi-header-has-breadcrumbs', false);
});

it('shows each client location count instead of its address in the table', function () {
    $address = '12345 Sound Street, Suite 678, Portland, Oregon 97201';
    $client = Client::factory()->create(['address' => $address]);
    ClientLocation::factory()
        ->count(2)
        ->for($client)
        ->create();

    Livewire::test(ListClients::class)
        ->assertCanSeeTableRecords([$client])
        ->assertCanRenderTableColumn('locations_count')
        ->assertTableColumnStateSet('locations_count', 2, $client)
        ->assertTableColumnDoesNotExist('address')
        ->assertDontSee($address);
});

it('creates a client from the Filament form', function () {
    $this->actingAs(User::factory()->create());

    $clientData = [
        'name' => 'Northwind Audio',
        'primary_contact_name' => 'Taylor Morgan',
        'phone' => '503-555-0164',
        'email' => 'taylor@northwind.example',
        'address' => "14 Sound Street\nPortland, OR",
    ];

    Livewire::test(CreateClient::class)
        ->fillForm($clientData)
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    $this->assertDatabaseHas(Client::class, $clientData);
});

it('requires a client name', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateClient::class)
        ->fillForm(['name' => null])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required'])
        ->assertNotNotified()
        ->assertNoRedirect();

    $this->assertDatabaseCount('clients', 0);
});

it('validates the client email address', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateClient::class)
        ->fillForm([
            'name' => 'Northwind Audio',
            'email' => 'not-an-email',
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'email'])
        ->assertNotNotified()
        ->assertNoRedirect();

    $this->assertDatabaseCount('clients', 0);
});

it('updates a client from the Filament edit page', function () {
    $client = Client::factory()->create();

    $this->actingAs(User::factory()->create());

    Livewire::test(EditClient::class, ['record' => $client->getKey()])
        ->fillForm([
            'name' => 'Updated Client Name',
            'primary_contact_name' => 'Jordan Lee',
            'phone' => '415-555-0100',
            'email' => 'jordan@example.test',
            'address' => "100 Main Street\nSan Francisco, CA",
        ])
        ->call('save')
        ->assertNotified();

    $this->assertDatabaseHas(Client::class, [
        'id' => $client->getKey(),
        'name' => 'Updated Client Name',
        'primary_contact_name' => 'Jordan Lee',
        'phone' => '415-555-0100',
        'email' => 'jordan@example.test',
        'address' => "100 Main Street\nSan Francisco, CA",
    ]);
});

it('deletes a client from the Filament edit page', function () {
    $client = Client::factory()->create();

    $this->actingAs(User::factory()->create());

    Livewire::test(EditClient::class, ['record' => $client->getKey()])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    $this->assertDatabaseMissing(Client::class, ['id' => $client->getKey()]);
});
