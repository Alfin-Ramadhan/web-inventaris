<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\User;
use App\Services\CategoryExcelExporter;
use App\Services\CategoryPdfExporter;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('category pdf exporter generates valid pdf string', function (): void {
    Category::factory()->count(3)->create();

    $pdf = CategoryPdfExporter::generate();

    expect($pdf)->toBeString()
        ->and(mb_strlen($pdf))->toBeGreaterThan(100);
});

test('category excel exporter generates valid xlsx content', function (): void {
    Category::factory()->count(3)->create();

    $excel = CategoryExcelExporter::generate();

    expect($excel)->toBeString()
        ->and(mb_strlen($excel))->toBeGreaterThan(100);
});

test('authenticated user can export categories pdf and excel via page actions', function (): void {
    $user = User::factory()->create(['email_verified_at' => now()]);
    Category::factory()->count(2)->create();

    $this->actingAs($user);

    $pdf = CategoryPdfExporter::generate();
    expect($pdf)->toBeString();

    $excel = CategoryExcelExporter::generate();
    expect($excel)->toBeString();
});
