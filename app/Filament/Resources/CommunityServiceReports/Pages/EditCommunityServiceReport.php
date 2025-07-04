<?php

namespace App\Filament\Resources\CommunityServiceReports\Pages;

use App\Filament\Resources\CommunityServiceReports\CommunityServiceReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCommunityServiceReport extends EditRecord
{
    protected static string $resource = CommunityServiceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
