<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditStudentProfile extends EditRecord
{
    protected static string $resource = StudentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('View Profile'),

            Action::make('linkToUser')
                ->label('Link to User Account')
                ->icon('heroicon-o-link')
                ->color('success')
                ->visible(fn () => !$this->record->user_id)
                ->action(function () {
                    // This would open a modal to select a user
                    // For now, just show a notification
                    Notification::make()
                        ->title('Feature not implemented')
                        ->body('User linking functionality would be implemented here')
                        ->info()
                        ->send();
                }),

            Action::make('unlinkFromUser')
                ->label('Unlink from User')
                ->icon('heroicon-o-x-mark')
                ->color('warning')
                ->visible(fn () => $this->record->user_id)
                ->action(function () {
                    $this->record->update([
                        'user_id' => null,
                        'status' => 'unclaimed'
                    ]);

                    Notification::make()
                        ->title('Profile unlinked successfully')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Unlink from User Account')
                ->modalDescription('This will remove the connection between this profile and the user account.'),

            DeleteAction::make()
                ->label('Delete Profile'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Student profile updated successfully';
    }
}
