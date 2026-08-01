<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;

it('can login', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'password' => bcrypt('password'),
    ]);

    visit('/')
        ->waitForText('Sign in')
        ->type('[wire\:model="data.email"]', $user->email)
        ->type('[wire\:model="data.password"]', 'password')
        ->click('button[type="submit"]')
        ->waitForText('Dashboard');
});

it('can register', function (): void {
    $count = User::query()->count();

    visit('/register')
        ->waitForText('Name')
        ->type('[wire\:model="data.name"]', 'Test User')
        ->type('[wire\:model="data.email"]', 'test@example.com')
        ->type('[wire\:model="data.password"]', 'password')
        ->type('[wire\:model="data.passwordConfirmation"]', 'password')
        ->click('button[type="submit"]')
        ->wait(3);

    expect(User::query()->count())->toBe($count + 1);
});
