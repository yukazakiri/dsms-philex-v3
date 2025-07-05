<?php

namespace App\Filament\Resources\ScholarshipPrograms\Pages;

use App\Filament\Resources\ScholarshipPrograms\ScholarshipProgramResource;
use App\Filament\Resources\ScholarshipPrograms\Widgets\StudenScholarshipStatsOverview;
use App\Filament\Resources\StudentProfiles\StudentProfileResource;
use App\Models\ScholarshipProgram;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ListScholarshipPrograms extends ListRecords
{
    protected static string $resource = ScholarshipProgramResource::class;

    public function getTitle(): string
    {
        return 'Scholarship Programs';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create New Program')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StudenScholarshipStatsOverview::class,
        ];
    }
}
