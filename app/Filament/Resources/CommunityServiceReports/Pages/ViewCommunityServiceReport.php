<?php

namespace App\Filament\Resources\CommunityServiceReports\Pages;

use App\Filament\Resources\CommunityServiceReports\CommunityServiceReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCommunityServiceReport extends ViewRecord
{
    protected static string $resource = CommunityServiceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
