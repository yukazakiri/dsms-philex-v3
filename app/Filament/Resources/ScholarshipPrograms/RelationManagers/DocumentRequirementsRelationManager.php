<?php

namespace App\Filament\Resources\ScholarshipPrograms\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;

class DocumentRequirementsRelationManager extends RelationManager
{
    protected static string $relationship = 'documentRequirements';

    protected static ?string $title = 'Document Requirements';

    protected static ?string $modelLabel = 'Document Requirement';

    protected static ?string $pluralModelLabel = 'Document Requirements';

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Document Name')
                ->required()
                ->maxLength(255)
                ->placeholder('e.g., Transcript of Records')
                ->helperText('Enter the name of the required document'),

            Textarea::make('description')
                ->label('Description')
                ->required()
                ->rows(3)
                ->placeholder('Describe what this document should contain and any specific requirements')
                ->helperText('Provide clear instructions for students about this document')
                ->columnSpanFull(),

            Toggle::make('is_required')
                ->label('Required Document')
                ->default(true)
                ->helperText('Toggle whether this document is mandatory for application'),
        ];
    }


}
