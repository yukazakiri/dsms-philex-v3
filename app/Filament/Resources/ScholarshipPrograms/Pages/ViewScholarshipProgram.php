<?php

namespace App\Filament\Resources\ScholarshipPrograms\Pages;

use App\Filament\Resources\ScholarshipPrograms\ScholarshipProgramResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewScholarshipProgram extends ViewRecord
{
    protected static string $resource = ScholarshipProgramResource::class;

    public function getTitle(): string
    {
        return 'Scholarship: ' . $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Program')
                ->icon('heroicon-o-pencil'),

            Action::make('toggleStatus')
                ->label(fn () => $this->record->active ? 'Deactivate Program' : 'Activate Program')
                ->icon(fn () => $this->record->active ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                ->color(fn () => $this->record->active ? 'warning' : 'success')
                ->action(function () {
                    $this->record->update(['active' => !$this->record->active]);

                    Notification::make()
                        ->title('Program ' . ($this->record->active ? 'activated' : 'deactivated') . ' successfully')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading(fn () => ($this->record->active ? 'Deactivate' : 'Activate') . ' Scholarship Program')
                ->modalDescription(fn () => 'Are you sure you want to ' . ($this->record->active ? 'deactivate' : 'activate') . ' this scholarship program?'),

            Action::make('viewApplications')
                ->label('View Applications')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->action(function () {
                    // This would redirect to applications for this scholarship
                    Notification::make()
                        ->title('Feature not implemented')
                        ->body('Scholarship applications view would be implemented here')
                        ->info()
                        ->send();
                }),

            Action::make('exportApplications')
                ->label('Export Applications')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    // This would export applications data
                    Notification::make()
                        ->title('Feature not implemented')
                        ->body('Export functionality would be implemented here')
                        ->info()
                        ->send();
                }),
        ];
    }
}
