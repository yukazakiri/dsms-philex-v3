<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Users\Widgets\UserStatsOverview;
use App\Filament\Resources\Users\Widgets\UserStatsWidget;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create User')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsOverview::class,
        ];
    }
}


