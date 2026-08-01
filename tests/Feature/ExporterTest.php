<?php

declare(strict_types=1);

use App\Filament\Exports\BorrowingExporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('borrowing exporter columns', function (): void {
    $columns = BorrowingExporter::getColumns();

    expect($columns)->toBeArray()
        ->and($columns)->not->toBeEmpty();
});

test('borrowing exporter notification body', function (): void {
    $export = new Export([
        'successful_rows' => 5,
        'total_rows' => 5,
    ]);

    $body = BorrowingExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('5 baris')
        ->and($body)->toContain('berhasil diekspor');
});

test('borrowing exporter notification body with failures', function (): void {
    $export = new Export([
        'successful_rows' => 5,
        'total_rows' => 7,
    ]);

    $body = BorrowingExporter::getCompletedNotificationBody($export);

    expect($body)->toContain('5 baris')
        ->and($body)->toContain('2 baris gagal diekspor');
});
