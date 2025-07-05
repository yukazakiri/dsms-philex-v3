<?php

namespace App\Filament\Resources\ScholarshipPrograms\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class ScholarshipProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // General Information
                TextInput::make('name')
                    ->label('Scholarship Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter scholarship program name'),

                Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->rows(4)
                    ->placeholder('Describe the scholarship program, its purpose, and goals')
                    ->columnSpanFull(),

                Select::make('semester')
                    ->label('Semester')
                    ->required()
                    ->options([
                        '1st Semester' => '1st Semester',
                        '2nd Semester' => '2nd Semester',
                        'Summer Term' => 'Summer Term',
                        'Annual' => 'Annual',
                    ])
                    ->native(false),

                TextInput::make('academic_year')
                    ->label('Academic Year')
                    ->required()
                    ->placeholder('YYYY-YYYY (e.g., 2024-2025)')
                    ->maxLength(9),

                DatePicker::make('application_deadline')
                    ->label('Application Deadline')
                    ->required()
                    ->native(false)
                    ->displayFormat('M j, Y'),

                Toggle::make('active')
                    ->label('Scholarship is Active')
                    ->default(true),

                // Budget & Eligibility
                TextInput::make('total_budget')
                    ->label('Total Budget (₱)')
                    ->required()
                    ->rule('regex:/^\d+(\.\d{1,2})?$/')
                    ->placeholder('0.00')
                    ->helperText('Enter amount in Philippine Peso'),

                TextInput::make('per_student_budget')
                    ->label('Award per Student (₱)')
                    ->required()
                    ->rule('regex:/^\d+(\.\d{1,2})?$/')
                    ->placeholder('0.00')
                    ->helperText('Enter amount in Philippine Peso'),

                TextInput::make('available_slots')
                    ->label('Available Slots')
                    ->required()
                    ->rule('regex:/^\d+$/')
                    ->placeholder('Number of scholarship slots'),

                Select::make('school_type_eligibility')
                    ->label('School Type Eligibility')
                    ->required()
                    ->options([
                        'high_school' => 'High School Students',
                        'college' => 'College Students',
                        'both' => 'Both High School and College',
                    ])
                    ->native(false),

                TextInput::make('min_gpa')
                    ->label('Minimum GPA (0-100%)')
                    ->required()
                    ->rule('regex:/^\d+(\.\d{1,2})?$/')
                    ->placeholder('75.00')
                    ->helperText('Enter GPA as percentage (0-100)'),

                TextInput::make('min_units')
                    ->label('Minimum Units (College Only)')
                    ->rule('regex:/^\d+$/')
                    ->placeholder('12')
                    ->helperText('Leave empty for high school or if not applicable')
                    ->disabled(fn (callable $get) => $get('school_type_eligibility') === 'high_school')
                    ->dehydrated(fn (callable $get) => $get('school_type_eligibility') !== 'high_school'),

                TextInput::make('community_service_days')
                    ->label('Required Community Service Days')
                    ->required()
                    ->rule('regex:/^\d+$/')
                    ->default(5)
                    ->placeholder('5'),
            ]);
    }
}
