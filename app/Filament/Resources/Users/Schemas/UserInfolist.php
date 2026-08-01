<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('email_verified_at')
                    ->label('Email verified')
                    ->getStateUsing(fn (User $record): string => $record->email_verified_at ? 'Verified' : 'Not Verified')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Verified' ? 'success' : 'danger')
                    ->tooltip(fn (User $record): ?string => $record->email_verified_at ? 'Verified at '.$record->email_verified_at->format('M d, Y H:i') : null),
                TextEntry::make('role')
                    ->badge(),
            ]);
    }
}
