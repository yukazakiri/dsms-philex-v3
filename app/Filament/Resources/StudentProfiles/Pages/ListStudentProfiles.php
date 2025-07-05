<?php

namespace App\Filament\Resources\StudentProfiles\Pages;

use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use App\Filament\Resources\StudentProfiles\Widgets\StudentStatsOverview;
use App\Models\StudentProfile;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ListStudentProfiles extends ListRecords
{
    protected static string $resource = StudentProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Student Profile')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StudentStatsOverview::class,
        ];
    }
}


