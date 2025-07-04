<?php

namespace App\Filament\Resources\ScholarshipPrograms\Widgets;

use App\Models\ScholarshipProgram;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudenScholarshipStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalPrograms = ScholarshipProgram::count();
        $activePrograms = ScholarshipProgram::where('active', true)->count();
        $totalBudget = ScholarshipProgram::where('active', true)->sum('total_budget');
        $totalSlots = ScholarshipProgram::where('active', true)->sum('available_slots');

        return [
            Stat::make('Total Programs', $totalPrograms)
                ->description('All scholarship programs')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary'),

            Stat::make('Active Programs', $activePrograms)
                ->description('Currently accepting applications')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Total Budget', '₱' . number_format($totalBudget, 2))
                ->description('Combined budget of active programs')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),

            Stat::make('Available Slots', $totalSlots)
                ->description('Total scholarship slots available')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),
        ];
    }
}
