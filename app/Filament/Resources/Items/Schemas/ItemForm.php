<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;

final class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Group::make()
                    ->schema([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Kategori')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Nama Barang')
                            ->required(),
                        TextInput::make('inventory_number')
                            ->label('Nomor Inventaris')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ])
                    ->columnSpan(1),
            ]);
    }
}
