<?php

use App\Models\Kit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows total kits and pending cleaning count on the admin dashboard', function () {
    Kit::factory()->count(7)->create(['status' => 'available']);
    Kit::factory()->count(3)->create(['status' => 'pending_cleaning']);

    $this->actingAs(User::factory()->create());

    $this->get('/admin')
        ->assertOk()
        ->assertSeeInOrder(['Total Kits', '10', 'Pending Cleaning', '3'])
        ->assertSee('<span style="color: oklch(0.828 0.189 84.429);">3</span>', false)
        ->assertDontSee('Active Kits')
        ->assertDontSee('In Transit Kits')
        ->assertDontSee('<h1 class="fi-header-heading">', false);
});
