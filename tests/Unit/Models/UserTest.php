<?php

declare(strict_types=1);

use App\Models\User;
use Filament\Panel;

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'role',
            'created_at',
            'updated_at',
        ]);
});

test('can access panel if email is verified', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $panel = mock(Panel::class);

    expect($user->canAccessPanel($panel))->toBeTrue();
});

test('cannot access panel if email is not verified', function (): void {
    $user = User::factory()->create(['email_verified_at' => null]);
    $panel = mock(Panel::class);

    expect($user->canAccessPanel($panel))->toBeFalse();
});
