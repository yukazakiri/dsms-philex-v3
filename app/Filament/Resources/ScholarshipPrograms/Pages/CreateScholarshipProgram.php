<?php

namespace App\Filament\Resources\ScholarshipPrograms\Pages;

use App\Filament\Resources\ScholarshipPrograms\ScholarshipProgramResource;
use Filament\Resources\Pages\CreateRecord;

class CreateScholarshipProgram extends CreateRecord
{
    protected static string $resource = ScholarshipProgramResource::class;

    public function getTitle(): string
    {
        return 'Create New Scholarship Program';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure min_units is null for high school eligibility
        if ($data['school_type_eligibility'] === 'high_school') {
            $data['min_units'] = null;
        }

        // Convert text input values to proper numeric types
        if (isset($data['total_budget'])) {
            $data['total_budget'] = (float) str_replace(',', '', $data['total_budget']);
        }
        if (isset($data['per_student_budget'])) {
            $data['per_student_budget'] = (float) str_replace(',', '', $data['per_student_budget']);
        }
        if (isset($data['available_slots'])) {
            $data['available_slots'] = (int) $data['available_slots'];
        }
        if (isset($data['min_gpa'])) {
            $data['min_gpa'] = (float) $data['min_gpa'];
        }
        if (isset($data['min_units']) && $data['min_units'] !== null) {
            $data['min_units'] = (int) $data['min_units'];
        }
        if (isset($data['community_service_days'])) {
            $data['community_service_days'] = (int) $data['community_service_days'];
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Scholarship program created successfully';
    }
}
