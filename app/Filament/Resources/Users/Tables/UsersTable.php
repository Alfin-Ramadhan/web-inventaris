<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (User $record): string => $record->email ?? ''),
                TextColumn::make('email_verified_at')
                    ->label('Email verified')
                    ->getStateUsing(fn (User $record): string => $record->email_verified_at ? 'Verified' : 'Not Verified')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Verified' ? 'success' : 'danger')
                    ->tooltip(fn (User $record): ?string => $record->email_verified_at ? 'Verified at '.$record->email_verified_at->format('M d, Y H:i') : null),
                TextColumn::make('role')
                    ->badge(),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->label('Email verification')
                    ->nullable()
                    ->trueLabel('Verified')
                    ->falseLabel('Not verified')
                    ->queries(
                        fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'),
                        fn (Builder $query): Builder => $query->whereNull('email_verified_at'),
                    ),
                SelectFilter::make('role')
                    ->options(UserRole::class),
            ], layout: FiltersLayout::AfterContent)
            ->defaultPaginationPageOption(5)
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
