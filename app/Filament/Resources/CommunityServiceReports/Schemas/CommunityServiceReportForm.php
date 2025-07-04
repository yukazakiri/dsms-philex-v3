<?php

namespace App\Filament\Resources\CommunityServiceReports\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CommunityServiceReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('scholarship_application_id')
                    ->relationship('scholarshipApplication', 'id')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('days_completed')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('pending_review'),
                Textarea::make('rejection_reason')
                    ->columnSpanFull(),
                DateTimePicker::make('submitted_at')
                    ->required(),
                DateTimePicker::make('reviewed_at'),
                TextInput::make('pdf_report_path'),
                TextInput::make('report_type')
                    ->required()
                    ->default('tracked'),
                TextInput::make('total_hours')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
