<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\Tables;

use App\Enums\ItemStatus;
use App\Models\Borrowing;
use App\Models\Item;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable(),
                TextColumn::make('inventory_number')
                    ->label('Nomor Inventaris')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn (Item $record): ItemStatus => $record->available_quantity >= 1 ? ItemStatus::Available : ItemStatus::NotAvailable)
                    ->badge()
                    ->color(fn (ItemStatus $state): string => match ($state) {
                        ItemStatus::Available => 'success',
                        ItemStatus::NotAvailable => 'danger',
                    }),
            ])
            ->filters([
                TernaryFilter::make('available')
                    ->label('Ketersediaan')
                    ->placeholder('Semua')
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia')
                    ->queries(
                        true: fn (Builder $query) => $query->whereDoesntHave('borrowings', fn ($q) => $q->whereNull('returned_at')),
                        false: fn (Builder $query) => $query->whereHas('borrowings', fn ($q) => $q->whereNull('returned_at')),
                    ),
            ], layout: FiltersLayout::AfterContent)
            ->defaultPaginationPageOption(5)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('borrow')
                    ->label('Pinjam')
                    ->color('success')
                    ->icon('heroicon-o-shopping-cart')
                    ->visible(fn (Item $record): bool => $record->available_quantity >= 1)
                    ->action(function (Item $record): void {
                        Borrowing::query()->create([
                            'user_id' => auth()->id(),
                            'item_id' => $record->id,
                            'borrowed_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
