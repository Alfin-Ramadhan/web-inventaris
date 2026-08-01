<?php

declare(strict_types=1);

use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('category total_quantity and available_quantity calculation', function (): void {
    $category = Category::factory()->create();

    $item1 = Item::factory()->create(['category_id' => $category->id]);
    $item2 = Item::factory()->create(['category_id' => $category->id]);

    expect($category->fresh()->total_quantity)->toBe(2);
    expect($category->fresh()->available_quantity)->toBe(2);

    $user = User::factory()->create();
    Borrowing::query()->create([
        'user_id' => $user->id,
        'item_id' => $item1->id,
        'borrowed_at' => now(),
    ]);

    expect($category->fresh()->total_quantity)->toBe(2);
    expect($category->fresh()->available_quantity)->toBe(1);
});
