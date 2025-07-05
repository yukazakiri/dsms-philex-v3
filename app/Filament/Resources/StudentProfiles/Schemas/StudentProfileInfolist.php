<?php

namespace App\Filament\Resources\StudentProfiles\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StudentProfileInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Student Identity
                TextEntry::make('display_name')
                    ->label('Full Name')
                    // ->state(function ($record) {
                    //     if ($record->user) {
                    //         return $record->user->name;
                    //     }
                    //     return trim($record->first_name . ' ' . $record->last_name) ?: 'No Name';
                    // })
                    ->size('lg')
                    ->weight('bold'),

                TextEntry::make('display_email')
                    ->label('Email Address')
                    ->state(function ($record) {
                        return $record->user ? $record->user->email : $record->email;
                    })
                    ->icon('heroicon-o-envelope')
                    ->copyable(),

                TextEntry::make('status')
                    ->label('Profile Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'claimed' => 'success',
                        'unclaimed' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    })
                    ->state(fn (string $state): string => ucfirst($state)),

                // Personal Information
                TextEntry::make('student_id')
                    ->label('Student ID')
                    ->placeholder('Not set')
                    ->icon('heroicon-o-hashtag'),

                TextEntry::make('phone_number')
                    ->label('Phone Number')
                    ->icon('heroicon-o-phone'),

                TextEntry::make('full_address')
                    ->label('Address')
                    ->state(function ($record) {
                        return $record->address . ', ' . $record->city . ', ' . $record->state . ' ' . $record->zip_code;
                    })
                    ->icon('heroicon-o-map-pin'),

                // Academic Information
                TextEntry::make('school_name')
                    ->label('School')
                    ->icon('heroicon-o-building-office'),

                TextEntry::make('school_type')
                    ->label('School Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high_school' => 'info',
                        'college' => 'success',
                        default => 'gray',
                    })
                    ->state(fn (string $state): string => match ($state) {
                        'high_school' => 'High School',
                        'college' => 'College',
                        default => ucfirst($state),
                    }),

                TextEntry::make('school_level')
                    ->label('School Level')
                    ->icon('heroicon-o-academic-cap'),

                TextEntry::make('gpa')
                    ->label('GPA')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('Not set')
                    ->icon('heroicon-o-star'),

                // System Information
                TextEntry::make('user.name')
                    ->label('Associated User Account')
                    ->placeholder('No user account linked')
                    ->icon('heroicon-o-user')
                    ->visible(fn ($record) => $record->user_id),

                TextEntry::make('created_at')
                    ->label('Profile Created')
                    ->dateTime('M j, Y g:i A')
                    ->icon('heroicon-o-calendar'),

                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y g:i A')
                    ->icon('heroicon-o-pencil'),
            ]);
    }
}
