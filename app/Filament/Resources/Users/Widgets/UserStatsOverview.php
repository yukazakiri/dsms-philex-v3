<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $adminUsers = User::where('role', 'admin')->count();
        $studentUsers = User::where('role', 'student')->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('All registered users')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Active Users', $activeUsers)
                ->description('Currently active users')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Administrators', $adminUsers)
                ->description('Admin users')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('warning'),

            Stat::make('Students', $studentUsers)
                ->description('Student users')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info'),
        ];
    }
}
