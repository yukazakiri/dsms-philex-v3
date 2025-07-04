<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF'),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'warning',
                        'student' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->placeholder('Never')
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->placeholder('Not verified')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                        'pending' => 'Pending',
                    ])
                    ->placeholder('All statuses'),

                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'student' => 'Student',
                    ])
                    ->placeholder('All roles'),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View'),
                EditAction::make()
                    ->label('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
