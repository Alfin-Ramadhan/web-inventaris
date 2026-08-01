<?php

declare(strict_types=1);

use App\Filament\Resources\Borrowings\BorrowingResource;
use App\Filament\Resources\Borrowings\Pages\ManageBorrowings;
use App\Filament\Resources\Borrowings\Schemas\BorrowingForm;
use App\Filament\Resources\Items\Pages\ListItems;
use App\Filament\Widgets\MyBorrowings;
use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('borrowing model relationships', function (): void {
    $borrowing = Borrowing::factory()->create();

    expect($borrowing->user)->toBeInstanceOf(User::class);
    expect($borrowing->item)->toBeInstanceOf(Item::class);
});

test('borrowing resource configuration', function (): void {
    expect(BorrowingResource::form(Schema::make()))->toBeInstanceOf(Schema::class);

    $user = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($user);

    Livewire::test(ManageBorrowings::class)
        ->assertOk();
});

test('borrowing form and table schemas', function (): void {
    expect(BorrowingForm::configure(Schema::make()))->toBeInstanceOf(Schema::class);
});

test('item resource borrow action', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $item = Item::factory()->create(['quantity' => 1]);

    $this->actingAs($user);

    Livewire::test(ListItems::class)
        ->callTableAction('borrow', $item);

    expect(Borrowing::query()->count())->toBe(1);
    expect($item->fresh()->available_quantity)->toBe(0);
});

test('borrowing resource return action', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $item = Item::factory()->create(['quantity' => 1]);
    $borrowing = Borrowing::factory()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'returned_at' => null,
    ]);

    $this->actingAs($user);

    Livewire::test(ManageBorrowings::class)
        ->callTableAction('return', $borrowing);

    expect($borrowing->fresh()->returned_at)->not->toBeNull();
});

test('my borrowings widget return action', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $item = Item::factory()->create(['quantity' => 1]);
    $borrowing = Borrowing::factory()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'returned_at' => null,
    ]);

    $this->actingAs($user);

    Livewire::test(MyBorrowings::class)
        ->callTableAction('return', $borrowing);

    expect($borrowing->fresh()->returned_at)->not->toBeNull();
});

test('items table availability filter', function (): void {
    Item::factory()->create(['quantity' => 1]); // Available
    $item2 = Item::factory()->create(['quantity' => 1]);
    Borrowing::factory()->create(['item_id' => $item2->id, 'returned_at' => null]); // Unavailable

    $this->actingAs(User::factory()->create(['email_verified_at' => now()]));

    Livewire::test(ListItems::class)
        ->filterTable('available', true)
        ->assertCanSeeTableRecords(Item::query()->where('quantity', '>', 0)->whereDoesntHave('borrowings', fn ($q) => $q->whereNull('returned_at'))->get())
        ->filterTable('available', false)
        ->assertCanSeeTableRecords(Item::query()->whereHas('borrowings', fn ($q) => $q->whereNull('returned_at'))->get());
});

test('borrowing resource returned filter', function (): void {
    $borrowing1 = Borrowing::factory()->create(['returned_at' => now()]);
    $borrowing2 = Borrowing::factory()->create(['returned_at' => null]);

    $this->actingAs(User::factory()->create(['email_verified_at' => now()]));

    Livewire::test(ManageBorrowings::class)
        ->filterTable('returned', true)
        ->assertCanSeeTableRecords([$borrowing1])
        ->assertCanNotSeeTableRecords([$borrowing2])
        ->filterTable('returned', false)
        ->assertCanSeeTableRecords([$borrowing2])
        ->assertCanNotSeeTableRecords([$borrowing1]);
});

test('items table configuration', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $this->actingAs($user);

    Livewire::test(ListItems::class)
        ->assertOk();
});
