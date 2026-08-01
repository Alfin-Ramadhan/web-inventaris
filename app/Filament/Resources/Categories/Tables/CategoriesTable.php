<?php

declare(strict_types=1);

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use App\Services\CategoryExcelExporter;
use App\Services\CategoryPdfExporter;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

final class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable(),
                TextColumn::make('total_quantity')
                    ->label('Total Stok')
                    ->state(fn (Category $record): int => $record->total_quantity),
                TextColumn::make('available_quantity')
                    ->label('Stok Tersedia')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger')
                    ->state(fn (Category $record): int => $record->available_quantity),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
                                    echo CategoryPdfExporter::generate($records);
                                },
                                sprintf('Kategori-Barang-Selected-%s.pdf', now()->format('Y-m-d')),
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
                                    echo CategoryExcelExporter::generate($records);
                                },
                                sprintf('Kategori-Barang-Selected-%s.xlsx', now()->format('Y-m-d')),
                                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                            );
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
