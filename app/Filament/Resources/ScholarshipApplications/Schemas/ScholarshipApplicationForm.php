<?php

namespace App\Filament\Resources\ScholarshipApplications\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ScholarshipApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_profile_id')
                    ->relationship('studentProfile', 'id')
                    ->required(),
                Select::make('scholarship_program_id')
                    ->relationship('scholarshipProgram', 'name')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                Textarea::make('admin_notes')
                    ->columnSpanFull(),
                DateTimePicker::make('submitted_at'),
                DateTimePicker::make('reviewed_at'),
            ]);
    }
}
