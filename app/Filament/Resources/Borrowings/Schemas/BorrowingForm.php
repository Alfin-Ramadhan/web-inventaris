<?php

declare(strict_types=1);

namespace App\Filament\Resources\Borrowings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

final class BorrowingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Group::make()
                    ->schema([
                        Select::make('user_id')
                            ->label('Peminjam')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('item_id')
                            ->label('Barang')
                            ->relationship('item', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('borrowed_at')
                            ->label('Tanggal Pinjam')
                            ->required()
                            ->default(now()),
                        DateTimePicker::make('due_at')
                            ->label('Batas Kembali'),
                        DateTimePicker::make('returned_at')
                            ->label('Tanggal Kembali'),
                    ])
                    ->columnSpan(1),
            ]);
    }
}
