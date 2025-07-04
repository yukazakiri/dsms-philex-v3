<?php

namespace App\Filament\Resources\ScholarshipApplications\Pages;

use App\Filament\Resources\ScholarshipApplications\ScholarshipApplicationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewScholarshipApplication extends ViewRecord
{
    protected static string $resource = ScholarshipApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
