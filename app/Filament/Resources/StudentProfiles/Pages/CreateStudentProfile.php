<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentProfile extends CreateRecord
{
    protected static string $resource = StudentProfileResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default status for new profiles
        $data['status'] = 'unclaimed';

        // Ensure user_id is null for new profiles created by admin
        $data['user_id'] = null;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Student profile created successfully';
    }
}
