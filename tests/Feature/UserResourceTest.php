<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('lists users with driver status and places the navigation link above item categories', function () {
    $user = User::factory()->create([
        'name' => 'Jordan Lee',
        'email' => 'jordan@example.test',
    ]);
    $user->driver()->create([
        'phone' => '503-555-0100',
        'status' => 'active',
    ]);

    $this->actingAs(User::factory()->create())
        ->get('/admin/users')
        ->assertOk()
        ->assertSeeInOrder(['Users', 'Item Categories']);

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords([$user])
        ->assertTableColumnStateSet('driver.phone', '503-555-0100', $user)
        ->assertTableColumnStateSet('driver.status', 'active', $user);
});

it('sorts users by driver status and phone', function () {
    $activeDriver = User::factory()->create();
    $activeDriver->driver()->create([
        'phone' => '503-555-0100',
        'status' => 'active',
    ]);

    $suspendedDriver = User::factory()->create();
    $suspendedDriver->driver()->create([
        'phone' => '503-555-0200',
        'status' => 'suspended',
    ]);

    Livewire::test(ListUsers::class)
        ->sortTable('driver.status')
        ->assertCanSeeTableRecords([$activeDriver, $suspendedDriver], inOrder: true)
        ->sortTable('driver.phone')
        ->assertCanSeeTableRecords([$activeDriver, $suspendedDriver], inOrder: true)
        ->sortTable('driver.phone', 'desc')
        ->assertCanSeeTableRecords([$suspendedDriver, $activeDriver], inOrder: true);
});

it('creates a user with an active driver profile when selected', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Dylan Brody',
            'email' => 'dylan@example.test',
            'password' => 'secure-password',
            'is_driver' => true,
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    $user = User::query()->where('email', 'dylan@example.test')->sole();
    $this->assertDatabaseHas('drivers', [
        'user_id' => $user->getKey(),
        'status' => 'active',
    ]);
    expect(Hash::check('secure-password', $user->password))->toBeTrue()
        ->and($user->isDriver())->toBeTrue();
});

it('creates a regular user without a driver profile by default', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Taylor Morgan',
            'email' => 'taylor@example.test',
            'password' => 'secure-password',
        ])
        ->call('create')
        ->assertNotified();

    $user = User::query()->where('email', 'taylor@example.test')->sole();
    $this->assertDatabaseCount('drivers', 0);
    expect($user->isDriver())->toBeFalse();
});

it('requires a name, unique email, and password when creating a user', function () {
    User::factory()->create([
        'email' => 'existing@example.test',
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => null,
            'email' => 'existing@example.test',
            'password' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'email' => 'unique',
            'password' => 'required',
        ])
        ->assertNotNotified()
        ->assertNoRedirect();

    $this->assertDatabaseCount('users', 2);
});

it('updates a user and adds or removes their driver profile', function () {
    $user = User::factory()->create();
    $originalPassword = $user->password;

    $this->actingAs(User::factory()->create());

    Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->assertFormSet(['is_driver' => false])
        ->fillForm([
            'name' => 'Updated User',
            'email' => $user->email,
            'is_driver' => true,
        ])
        ->call('save')
        ->assertNotified();

    $user->refresh();
    $this->assertDatabaseHas('drivers', [
        'user_id' => $user->getKey(),
        'status' => 'active',
    ]);
    expect($user->name)->toBe('Updated User')
        ->and($user->password)->toBe($originalPassword)
        ->and($user->isDriver())->toBeTrue();

    Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->assertFormSet(['is_driver' => true])
        ->fillForm([
            'name' => 'Updated User',
            'email' => $user->email,
            'is_driver' => false,
        ])
        ->call('save')
        ->assertNotified();

    $this->assertDatabaseMissing('drivers', [
        'user_id' => $user->getKey(),
    ]);
    expect($user->refresh()->isDriver())->toBeFalse();
});

it('deletes a user and their driver profile', function () {
    $user = User::factory()->create();
    $user->driver()->create(['status' => 'active']);

    $this->actingAs(User::factory()->create());

    Livewire::test(EditUser::class, ['record' => $user->getKey()])
        ->callAction(DeleteAction::class)
        ->assertNotified()
        ->assertRedirect();

    $this->assertDatabaseMissing(User::class, ['id' => $user->getKey()]);
    $this->assertDatabaseMissing('drivers', ['user_id' => $user->getKey()]);
});
