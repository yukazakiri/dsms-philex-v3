<?php

namespace App\Filament\Resources\StudentProfiles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Personal Information
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter first name'),

                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter last name'),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(table: 'student_profiles', column: 'email', ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('student@example.com'),

                // Contact & Address Information
                TextInput::make('phone_number')
                    ->label('Phone Number')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->placeholder('09XX-XXX-XXXX (Philippines format)'),

                TextInput::make('address')
                    ->label('Street Address')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('123 Main St'),

                TextInput::make('city')
                    ->label('City')
                    ->required()
                    ->maxLength(255),

                TextInput::make('state')
                    ->label('State / Province')
                    ->required()
                    ->maxLength(255),

                TextInput::make('zip_code')
                    ->label('ZIP / Postal Code')
                    ->required()
                    ->maxLength(10)
                    ->placeholder('4-digit Philippines postal code'),

                // Academic Information
                Select::make('school_type')
                    ->label('School Type')
                    ->required()
                    ->options([
                        'high_school' => 'High School',
                        'college' => 'College',
                    ])
                    ->native(false),

                TextInput::make('school_level')
                    ->label('School Level')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Grade 11, Sophomore'),

                TextInput::make('school_name')
                    ->label('School Name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter school name'),

                TextInput::make('student_id')
                    ->label('Student ID Number')
                    ->maxLength(255)
                    ->placeholder('Optional'),

                TextInput::make('gpa')
                    ->label('GPA')
                    ->numeric()
                    ->step(0.01)
                    ->minValue(0)
                    ->maxValue(4.0)
                    ->placeholder('e.g., 3.75'),

                // System fields
                Select::make('user_id')
                    ->label('Associated User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a user (optional)')
                    ->visible(fn (string $context): bool => $context === 'edit'),

                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'unclaimed' => 'Unclaimed',
                        'claimed' => 'Claimed',
                        'archived' => 'Archived',
                    ])
                    ->default('unclaimed')
                    ->native(false)
                    ->visible(fn (string $context): bool => $context === 'edit'),
            ]);
    }
}
