<?php

namespace App\Filament\Resources\ScholarshipPrograms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ScholarshipProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->semester . ' | ' . $record->academic_year),

                TextColumn::make('budget_display')
                    ->label('Budget')
                    ->getStateUsing(function ($record) {
                        return '₱' . number_format($record->per_student_budget, 2) . ' / student';
                    })
                    ->description(fn ($record) => 'Total: ₱' . number_format($record->total_budget, 2))
                    ->sortable(['per_student_budget']),

                TextColumn::make('school_type_eligibility')
                    ->label('Eligibility')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high_school' => 'info',
                        'college' => 'success',
                        'both' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'high_school' => 'High School',
                        'college' => 'College',
                        'both' => 'All Students',
                        default => ucfirst($state),
                    }),

                TextColumn::make('application_deadline')
                    ->label('Deadline')
                    ->date('M j, Y')
                    ->sortable()
                    ->color(fn ($record) => $record->application_deadline->isPast() ? 'danger' : 'gray'),

                TextColumn::make('scholarship_applications_count')
                    ->label('Applications')
                    ->counts('scholarshipApplications')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('active')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive'),

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
                SelectFilter::make('school_type_eligibility')
                    ->label('Eligibility')
                    ->options([
                        'high_school' => 'High School',
                        'college' => 'College',
                        'both' => 'All Students',
                    ])
                    ->placeholder('All Eligibilities'),

                SelectFilter::make('active')
                    ->label('Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ])
                    ->placeholder('All Statuses'),

                SelectFilter::make('semester')
                    ->label('Semester')
                    ->options([
                        '1st Semester' => '1st Semester',
                        '2nd Semester' => '2nd Semester',
                        'Summer Term' => 'Summer Term',
                        'Annual' => 'Annual',
                    ])
                    ->placeholder('All Semesters'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View'),
                EditAction::make()
                    ->label('Edit'),
                DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Scholarship Program?')
                    ->modalDescription(function ($record) {
                        $applicationsCount = $record->scholarshipApplications()->count();
                        return "This action cannot be undone. This will permanently delete the scholarship program and ALL related data including:\n\n" .
                               "• {$applicationsCount} applications\n" .
                               "• All document uploads and files\n" .
                               "• All community service reports\n" .
                               "• All disbursement records\n\n" .
                               "Are you sure you want to proceed?";
                    })
                    ->modalSubmitActionLabel('Delete')
                    ->color('danger'),
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
