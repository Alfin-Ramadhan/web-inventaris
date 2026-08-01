<?php

declare(strict_types=1);

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use App\Services\BorrowingExcelExporter;
use App\Services\BorrowingPdfExporter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('borrowing pdf exporter generates valid pdf string', function (): void {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    Borrowing::query()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'borrowed_at' => now(),
    ]);

    $pdf = BorrowingPdfExporter::generate();

    expect($pdf)->toBeString()
        ->and(mb_strlen($pdf))->toBeGreaterThan(100);
});

test('borrowing excel exporter generates valid xlsx content', function (): void {
    $user = User::factory()->create();
    $item = Item::factory()->create();
    Borrowing::query()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'borrowed_at' => now(),
    ]);

    $excel = BorrowingExcelExporter::generate();

    expect($excel)->toBeString()
        ->and(mb_strlen($excel))->toBeGreaterThan(100);
});
