<?php

declare(strict_types=1);

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can borrow an item', function (): void {
    $user = User::factory()->create();
    $item = Item::factory()->create(['quantity' => 1]);

    $this->actingAs($user);

    Borrowing::query()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'borrowed_at' => now(),
    ]);

    expect($item->fresh()->available_quantity)->toBe(0);
    expect($user->borrowings)->toHaveCount(1);
});

test('an item is not available if quantity is zero', function (): void {
    $item = Item::factory()->create(['quantity' => 0]);

    expect($item->available_quantity)->toBe(0);
});

test('an item availability decreases as it is borrowed', function (): void {
    $item = Item::factory()->create(['quantity' => 5]);
    $users = User::factory()->count(3)->create();

    foreach ($users as $user) {
        Borrowing::query()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'borrowed_at' => now(),
        ]);
    }

    expect($item->fresh()->available_quantity)->toBe(2);
});

test('returning an item increases its available quantity', function (): void {
    $user = User::factory()->create();
    $item = Item::factory()->create(['quantity' => 1]);

    $borrowing = Borrowing::query()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'borrowed_at' => now(),
    ]);

    expect($item->fresh()->available_quantity)->toBe(0);

    $borrowing->update(['returned_at' => now()]);

    expect($item->fresh()->available_quantity)->toBe(1);
});
