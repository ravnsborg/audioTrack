<?php

use App\Models\Kit;
use App\Models\KitStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows kit totals by status on the admin dashboard', function () {
    $activeStatus = KitStatus::factory()->create(['name' => 'active']);
    $inTransitStatus = KitStatus::factory()->create(['name' => 'in_transit']);
    $pendingCleaningStatus = KitStatus::factory()->create(['name' => 'pending_cleaning']);
    $availableStatus = KitStatus::factory()->create(['name' => 'available']);

    Kit::factory()->count(2)->for($activeStatus, 'kitStatus')->create();
    Kit::factory()->for($inTransitStatus, 'kitStatus')->create();
    Kit::factory()->count(3)->for($pendingCleaningStatus, 'kitStatus')->create();
    Kit::factory()->for($availableStatus, 'kitStatus')->create();

    $this->actingAs(User::factory()->create());

    $this->get('/admin')
        ->assertOk()
        ->assertSeeInOrder([
            'Total Kits',
            '7',
            'Active Kits',
            '2',
            'In Transit Kits',
            '1',
            'Pending Cleaning',
            '3',
        ])
        ->assertSee('<span style="color: #86efac;">2</span>', false)
        ->assertSee('<span style="color: oklch(0.51 0.26 276.81);">1</span>', false)
        ->assertSee('<span style="color: #fbbf24;">3</span>', false);
});
