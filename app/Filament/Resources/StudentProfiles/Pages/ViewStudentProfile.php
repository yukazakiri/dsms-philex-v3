<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewStudentProfile extends ViewRecord
{
    protected static string $resource = StudentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit Profile')
                ->icon('heroicon-o-pencil'),

            Action::make('viewApplications')
                ->label('View Applications')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->visible(fn () => $this->record->user_id)
                ->action(function () {
                    // This would redirect to scholarship applications for this student
                    // For now, just show a notification
                    Notification::make()
                        ->title('Feature not implemented')
                        ->body('Scholarship applications view would be implemented here')
                        ->info()
                        ->send();
                }),

            Action::make('sendInvitation')
                ->label('Send Registration Invitation')
                ->icon('heroicon-o-envelope')
                ->color('success')
                ->visible(fn () => !$this->record->user_id && $this->record->status === 'unclaimed')
                ->action(function () {
                    // This would send an email invitation to register
                    // For now, just show a notification
                    Notification::make()
                        ->title('Feature not implemented')
                        ->body('Email invitation functionality would be implemented here')
                        ->info()
                        ->send();
                }),
        ];
    }
}
