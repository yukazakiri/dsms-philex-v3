<?php

namespace App\Filament\Resources\ScholarshipApplications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ScholarshipApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('studentProfile.id')
                    ->numeric(),
                TextEntry::make('scholarshipProgram.name')
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
            ]);
    }
}
