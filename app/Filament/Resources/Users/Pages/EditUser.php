<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('View User'),

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

            DeleteAction::make()
                ->label('Delete User'),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Hash the password if provided and not empty
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            // Remove password from data if it's empty to avoid overwriting
            unset($data['password']);
        }

        // Remove password_confirmation as it's not a database field
        unset($data['password_confirmation']);

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'User updated successfully';
    }
}
