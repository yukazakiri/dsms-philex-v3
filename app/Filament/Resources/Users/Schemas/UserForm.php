<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->description('Enter the user\'s basic information and settings.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Full Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter full name'),

                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->placeholder('Enter email address'),

                                Select::make('role')
                                    ->label('Role')
                                    ->required()
                                    ->options([
                                        'student' => 'Student',
                                        'admin' => 'Administrator',
                                    ])
                                    ->default('student')
                                    ->native(false),

                                Select::make('status')
                                    ->label('Status')
                                    ->required()
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'suspended' => 'Suspended',
                                        'pending' => 'Pending',
                                    ])
                                    ->default('active')
                                    ->native(false),

                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->rule(Password::default())
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->placeholder('Enter password'),

                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->same('password')
                                    ->dehydrated(false)
                                    ->placeholder('Confirm password')
                                    ->visible(fn (string $context): bool => $context === 'create'),
                            ]),
                    ]),

                FileUpload::make('avatar')
                    ->image()
                    ->directory('avatars')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->label('Profile Avatar'),

                FileUpload::make('cover_image')
                    ->image()
                    ->directory('cover-images')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->label('Cover Image'),

                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->displayFormat('M j, Y g:i A')
                    ->placeholder('Not verified')
                    ->visible(fn (string $context): bool => $context === 'edit'),

                DateTimePicker::make('last_login_at')
                    ->label('Last Login At')
                    ->disabled()
                    ->displayFormat('M j, Y g:i A')
                    ->placeholder('Never logged in')
                    ->visible(fn (string $context): bool => $context === 'edit'),

                TextInput::make('provider')
                    ->disabled()
                    ->placeholder('OAuth provider')
                    ->visible(fn ($record) => $record && $record->provider),

                TextInput::make('provider_id')
                    ->disabled()
                    ->placeholder('Provider user ID')
                    ->visible(fn ($record) => $record && $record->provider_id),

                TextInput::make('facebook_id')
                    ->disabled()
                    ->placeholder('Facebook ID')
                    ->visible(fn ($record) => $record && $record->facebook_id),
            ]);
    }
}
