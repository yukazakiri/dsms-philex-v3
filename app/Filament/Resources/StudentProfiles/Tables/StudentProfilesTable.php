<?php

namespace App\Filament\Resources\StudentProfiles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentProfilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Name')
                    ->getStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->name;
                        }
                        return trim($record->first_name . ' ' . $record->last_name) ?: 'No Name';
                    })
                    ->searchable(['first_name', 'last_name', 'user.name'])
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('display_email')
                    ->label('Email')
                    ->getStateUsing(function ($record) {
                        return $record->user ? $record->user->email : $record->email;
                    })
                    ->searchable(['email', 'user.email'])
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('student_id')
                    ->label('Student ID')
                    ->searchable()
                    ->placeholder('Not set')
                    ->fontFamily('mono'),

                TextColumn::make('school_name')
                    ->label('School')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),

                TextColumn::make('school_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high_school' => 'info',
                        'college' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'high_school' => 'High School',
                        'college' => 'College',
                        default => ucfirst($state),
                    }),

                TextColumn::make('gpa')
                    ->label('GPA')
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->placeholder('Not set'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'claimed' => 'success',
                        'unclaimed' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('school_type')
                    ->label('School Type')
                    ->options([
                        'high_school' => 'High School',
                        'college' => 'College',
                    ])
                    ->placeholder('All School Types'),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'claimed' => 'Claimed',
                        'unclaimed' => 'Unclaimed',
                        'archived' => 'Archived',
                    ])
                    ->placeholder('All Statuses'),
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
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
