<?php

namespace App\Filament\Resources\StudentProfiles\Widgets;

use App\Models\StudentProfile;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalProfiles = StudentProfile::count();
        $claimedProfiles = StudentProfile::where('status', 'claimed')->count();
        $unclaimedProfiles = StudentProfile::where('status', 'unclaimed')->count();
        $highSchoolProfiles = StudentProfile::where('school_type', 'high_school')->count();
        $collegeProfiles = StudentProfile::where('school_type', 'college')->count();

        return [
            Stat::make('Total Profiles', $totalProfiles)
                ->description('All student profiles')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Claimed Profiles', $claimedProfiles)
                ->description('Linked to user accounts')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Unclaimed Profiles', $unclaimedProfiles)
                ->description('Awaiting user registration')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('High School', $highSchoolProfiles)
                ->description('High school students')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('info'),

            Stat::make('College', $collegeProfiles)
                ->description('College students')
                ->descriptionIcon('heroicon-o-building-library')
                ->color('success'),
        ];
    }
}
