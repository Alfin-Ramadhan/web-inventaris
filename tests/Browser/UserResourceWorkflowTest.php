<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('performs full users resource workflow', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($user);

    visit('/users')
        ->assertSee($user->name);

    $email = 'newuser@example.test';
    $name = 'New User';
    $role = 'karyawan';

    $newUser = User::factory()->create([
        'name' => $name,
        'email' => $email,
        'role' => $role,
    ]);

    assertDatabaseHas('users', ['email' => $email, 'name' => $name]);

    visit('/users')->assertSee($newUser->name);

    visit('/users/'.$newUser->getRouteKey())->assertSee($newUser->name);

    $browserEdit = visit('/users/'.$newUser->getRouteKey().'/edit');

    $browserEdit->script('window.setUpUnsavedDataChangesAlert = function() { return; }; ');

    $browserEdit->type('[wire\\:model="data.name"]', 'Updated Name')
        ->click('button[wire\\:target="save"]')
        ->waitForText('Saved');

    $newUser->refresh();
    $this->assertSame('Updated Name', $newUser->name);

    assertDatabaseHas('users', ['email' => $email, 'name' => 'Updated Name']);

    visit('/users')->assertSee('Updated Name');

    $newUser->delete();

    assertDatabaseMissing('users', ['email' => $email]);
});
