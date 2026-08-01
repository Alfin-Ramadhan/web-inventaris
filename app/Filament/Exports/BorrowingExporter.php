<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\Borrowing;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

final class BorrowingExporter extends Exporter
{
    protected static ?string $model = Borrowing::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user.name')
                ->label('Peminjam'),
            ExportColumn::make('item.name')
                ->label('Barang'),
            ExportColumn::make('borrowed_at')
                ->label('Tanggal Pinjam'),
            ExportColumn::make('due_at')
                ->label('Batas Kembali'),
            ExportColumn::make('returned_at')
                ->label('Tanggal Kembali'),
            ExportColumn::make('created_at')
                ->label('Dibuat Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data peminjaman Anda telah selesai dan '.Number::format($export->successful_rows).' '.str('baris')->plural($export->successful_rows).' berhasil diekspor.';

        if (($failedRowsCount = $export->getFailedRowsCount()) !== 0) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('baris')->plural($failedRowsCount).' gagal diekspor.';
        }

        return $body;
    }
}
