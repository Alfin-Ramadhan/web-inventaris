<?php

declare(strict_types=1);

use App\Models\Item;
use App\Models\User;
use App\Services\ItemQrPdfGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('item qr pdf generator creates pdf content', function (): void {
    $item = Item::factory()->create();

    $pdf = ItemQrPdfGenerator::generate($item);

    expect($pdf)->toBeString()
        ->and(mb_strlen($pdf))->toBeGreaterThan(100);
});

test('authenticated user can download item qr pdf', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $item = Item::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('items.pdf', ['item' => $item->inventory_number]));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});
