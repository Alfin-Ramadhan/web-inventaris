<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Item;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('performs full items resource workflow', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($user);

    $browserCreate = visit('/items/create');

    $browserCreate->type('[wire\\:model="data.name"]', 'New UI Item')
        ->type('[wire\\:model="data.quantity"]', '10')
        ->click('button[wire\\:target="create"]')
        ->waitForText('Created');

    assertDatabaseHas('items', ['name' => 'New UI Item', 'quantity' => 10]);

    $newItem = Item::query()->where('name', 'New UI Item')->firstOrFail();

    visit('/items')
        ->assertSee($newItem->name)
        ->assertSee('tersedia'); // ItemStatus::Available enum value

    visit('/items/'.$newItem->getRouteKey())
        ->assertSee($newItem->name)
        ->assertSee('10')
        ->assertSee('tersedia');

    $browserEdit = visit('/items/'.$newItem->getRouteKey().'/edit');

    $browserEdit->script('window.setUpUnsavedDataChangesAlert = function() { return; }; ');

    $browserEdit->type('[wire\\:model="data.name"]', 'Updated UI Item')
        ->type('[wire\\:model="data.quantity"]', '0') // Change to 0
        ->click('button[wire\\:target="save"]')
        ->waitForText('Saved');

    $newItem->refresh();
    $this->assertSame('Updated UI Item', $newItem->name);
    $this->assertSame(0, $newItem->quantity);

    assertDatabaseHas('items', ['name' => 'Updated UI Item', 'quantity' => 0]);

    visit('/items')
        ->assertSee('Updated UI Item')
        ->assertSee('tidak tersedia'); // ItemStatus::NotAvailable

    $newItem->delete();

    assertDatabaseMissing('items', ['name' => 'Updated UI Item']);
});
