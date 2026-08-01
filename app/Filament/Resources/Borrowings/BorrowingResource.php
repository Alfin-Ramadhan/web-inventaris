<?php

declare(strict_types=1);

namespace App\Filament\Resources\Borrowings;

use App\Filament\Resources\Borrowings\Pages\ManageBorrowings;
use App\Filament\Resources\Borrowings\Schemas\BorrowingForm;
use App\Filament\Resources\Borrowings\Tables\BorrowingsTable;
use App\Models\Borrowing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class BorrowingResource extends Resource
{
    protected static ?string $model = Borrowing::class;

    protected static ?string $navigationLabel = 'Peminjaman';

    protected static ?string $modelLabel = 'Peminjaman';

    protected static ?string $pluralModelLabel = 'Data Peminjaman';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BorrowingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BorrowingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBorrowings::route('/'),
        ];
    }
}
