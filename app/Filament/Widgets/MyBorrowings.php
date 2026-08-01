<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Borrowing;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

final class MyBorrowings extends TableWidget
{
    protected static ?string $heading = 'Peminjaman Aktif';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Borrowing::query()
                ->where('user_id', auth()->id())
                ->whereNull('returned_at')
            )
            ->columns([
                TextColumn::make('item.name')
                    ->label('Barang')
                    ->searchable(),
                TextColumn::make('borrowed_at')
                    ->dateTime()
                    ->label('Dipinjam Pada'),
                TextColumn::make('due_at')
                    ->dateTime()
                    ->label('Batas Kembali')
                    ->placeholder('Tidak ada batas waktu'),
            ])
            ->recordActions([
                Action::make('return')
                    ->label('Kembalikan')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn (Borrowing $record) => $record->update(['returned_at' => now()]))
                    ->requiresConfirmation(),
            ]);
    }
}
