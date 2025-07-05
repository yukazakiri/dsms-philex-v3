<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;

use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('avatar')
                    ->label('Profile Avatar')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                    ->columnSpanFull(),

                TextEntry::make('name')
                    ->label('Full Name')
                    ->size('lg')
                    ->weight('bold'),

                TextEntry::make('email')
                    ->label('Email Address')
                    ->icon('heroicon-o-envelope')
                    ->copyable(),

                TextEntry::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'warning',
                        'student' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'suspended' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextEntry::make('email_verified_at')
                    ->label('Email Verified At')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('Not verified')
                    ->icon('heroicon-o-check-circle'),

                TextEntry::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('Never logged in')
                    ->icon('heroicon-o-clock'),

                TextEntry::make('created_at')
                    ->label('Account Created')
                    ->dateTime('M j, Y g:i A')
                    ->icon('heroicon-o-calendar'),

                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('M j, Y g:i A')
                    ->icon('heroicon-o-pencil'),

                TextEntry::make('provider')
                    ->label('Login Provider')
                    ->placeholder('Email')
                    ->formatStateUsing(fn (?string $state): string => $state ? ucfirst($state) : 'Email')
                    ->icon('heroicon-o-key')
                    ->visible(fn ($record) => $record->provider),

                TextEntry::make('facebook_id')
                    ->label('Facebook ID')
                    ->icon('heroicon-o-globe-alt')
                    ->visible(fn ($record) => $record->facebook_id),

                TextEntry::make('facebook_profile_url')
                    ->label('Facebook Profile')
                    ->url(fn ($record) => $record->facebook_profile_url)
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-link')
                    ->visible(fn ($record) => $record->facebook_profile_url),

                ImageEntry::make('cover_image')
                    ->label('Cover Image')
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record->cover_image),
            ]);
    }
}
