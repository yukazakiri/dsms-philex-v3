<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Hash the password if provided
        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        }

        // Remove password_confirmation as it's not a database field
        unset($data['password_confirmation']);

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'User created successfully';
    }
}
