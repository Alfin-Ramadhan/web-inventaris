<?php

declare(strict_types=1);

namespace App\Filament\Resources\Borrowings\Tables;

use App\Models\Borrowing;
use App\Services\BorrowingExcelExporter;
use App\Services\BorrowingPdfExporter;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class BorrowingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('item.name')
                    ->label('Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('borrowed_at')
                    ->label('Tanggal Pinjam')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label('Batas Kembali')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->label('Status')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Aktif'),
            ])
            ->filters([
                TernaryFilter::make('returned')
                    ->label('Status Kembali')
                    ->placeholder('Semua')
                    ->trueLabel('Dikembalikan')
                    ->falseLabel('Aktif')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('returned_at'),
                        false: fn (Builder $query) => $query->whereNull('returned_at'),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('return')
                    ->label('Kembalikan')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Borrowing $record): bool => $record->returned_at === null)
                    ->action(fn (Borrowing $record) => $record->update(['returned_at' => now()]))
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('exportSelectedPdf')
                        ->label('Export Selected PDF')
                        ->icon('heroicon-o-document-text')
                        ->color('danger')
                        ->action(function (Collection $records) {
                            return response()->streamDownload(
                                static function () use ($records): void {
                                    echo BorrowingPdfExporter::generate($records);
                                },
                                sprintf('Data-Peminjaman-Selected-%s.pdf', now()->format('Y-m-d')),
                                ['Content-Type' => 'application/pdf']
                            );
                        }),
                    BulkAction::make('exportSelectedExcel')
                        ->label('Export Selected Excel')
                        ->icon('heroicon-o-table-cells')
                        ->color('success')
                        ->action(function (Collection $records) {
                            return response()->streamDownload(
                                static function () use ($records): void {
                                    echo BorrowingExcelExporter::generate($records);
                                },
                                sprintf('Data-Peminjaman-Selected-%s.xlsx', now()->format('Y-m-d')),
                                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                            );
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
