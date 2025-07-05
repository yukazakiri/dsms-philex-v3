<?php

namespace App\Filament\Resources\CommunityServiceReports\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CommunityServiceReportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('scholarshipApplication.id')
                    ->numeric(),
                TextEntry::make('days_completed')
                    ->numeric(),
                TextEntry::make('status'),
                TextEntry::make('submitted_at')
                    ->dateTime(),
                TextEntry::make('reviewed_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('pdf_report_path'),
                TextEntry::make('report_type'),
                TextEntry::make('total_hours')
                    ->numeric(),
            ]);
    }
}
