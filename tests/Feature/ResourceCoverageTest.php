<?php

declare(strict_types=1);

use App\Filament\Resources\Items\ItemResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Item;
use App\Models\User;

it('returns correct global search details for item resource', function (): void {
    $item = new Item(['quantity' => 5]);

    expect(ItemResource::getGlobalSearchResultDetails($item))
        ->toBe(['Quantity' => '5']);
});

it('returns correct relations for item resource', function (): void {
    expect(ItemResource::getRelations())->toBe([]);
});

it('returns correct navigation badge for item resource', function (): void {
    Item::factory()->count(3)->create(['quantity' => 5]);
    Item::factory()->count(2)->create(['quantity' => 0]);

    expect(ItemResource::getNavigationBadge())->toBe('3');
});

it('returns correct globally searchable attributes for user resource', function (): void {
    expect(UserResource::getGloballySearchableAttributes())
        ->toBe(['name', 'email']);
});

it('returns correct global search details for user resource', function (): void {
    $user = new User(['email' => 'test@example.com']);

    expect(UserResource::getGlobalSearchResultDetails($user))
        ->toBe(['Email' => 'test@example.com']);
});

it('returns correct relations for user resource', function (): void {
    expect(UserResource::getRelations())->toBe([]);
});

it('returns correct navigation badge for user resource', function (): void {
    User::factory()->count(4)->create();

    expect(UserResource::getNavigationBadge())->toBe('4');
});
