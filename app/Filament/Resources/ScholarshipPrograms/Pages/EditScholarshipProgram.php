<?php

namespace App\Filament\Resources\ScholarshipPrograms\Pages;

use App\Filament\Resources\ScholarshipPrograms\ScholarshipProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditScholarshipProgram extends EditRecord
{
    protected static string $resource = ScholarshipProgramResource::class;

    public function getTitle(): string
    {
        return 'Edit Scholarship Program';
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('View Program'),

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

            DeleteAction::make()
                ->label('Delete Program')
                ->requiresConfirmation()
                ->modalHeading('Delete Scholarship Program')
                ->modalDescription('This will permanently delete this scholarship program and ALL related data. This action cannot be undone.'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Scholarship program updated successfully';
    }
}
