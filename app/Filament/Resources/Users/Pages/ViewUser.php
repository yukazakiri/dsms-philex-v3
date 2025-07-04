<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Password;
use Filament\Notifications\Notification;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Edit User')
                ->icon('heroicon-o-pencil'),

            Action::make('sendPasswordReset')
                ->label('Send Password Reset')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->action(function () {
                    $status = Password::sendResetLink(['email' => $this->record->email]);

                    if ($status === Password::RESET_LINK_SENT) {
                        Notification::make()
                            ->title('Password reset email sent successfully')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Failed to send password reset email')
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation()
                ->modalHeading('Send Password Reset Email')
                ->modalDescription('Are you sure you want to send a password reset email to this user?'),

            Action::make('forcePasswordChange')
                ->label('Force Password Change')
                ->icon('heroicon-o-lock-closed')
                ->color('danger')
                ->action(function () {
                    // This would require additional implementation in your User model
                    // For now, we'll just show a notification
                    Notification::make()
                        ->title('User will be required to change password on next login')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Force Password Change')
                ->modalDescription('This will require the user to change their password on their next login.'),
        ];
    }
}
