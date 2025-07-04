<?php

namespace App\Filament\Resources\CommunityServiceReports\Pages;

use App\Filament\Resources\CommunityServiceReports\CommunityServiceReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCommunityServiceReports extends ListRecords
{
    protected static string $resource = CommunityServiceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
