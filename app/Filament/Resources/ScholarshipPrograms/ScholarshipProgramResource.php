<?php

namespace App\Filament\Resources\ScholarshipPrograms;

use App\Filament\Resources\ScholarshipPrograms\Pages\CreateScholarshipProgram;
use App\Filament\Resources\ScholarshipPrograms\Pages\EditScholarshipProgram;
use App\Filament\Resources\ScholarshipPrograms\Pages\ListScholarshipPrograms;
use App\Filament\Resources\ScholarshipPrograms\Pages\ViewScholarshipProgram;
use App\Filament\Resources\ScholarshipPrograms\RelationManagers\DocumentRequirementsRelationManager;
use App\Filament\Resources\ScholarshipPrograms\Schemas\ScholarshipProgramForm;
use App\Filament\Resources\ScholarshipPrograms\Schemas\ScholarshipProgramInfolist;
use App\Filament\Resources\ScholarshipPrograms\Tables\ScholarshipProgramsTable;
use App\Models\ScholarshipProgram;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ScholarshipProgramResource extends Resource
{
    protected static ?string $model = ScholarshipProgram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Scholarship Programs';

    protected static ?string $modelLabel = 'Scholarship Program';

    protected static ?string $pluralModelLabel = 'Scholarship Programs';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ScholarshipProgramForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ScholarshipProgramInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScholarshipProgramsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['scholarshipApplications']) // Eager load applications for counts
            ->orderBy('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            DocumentRequirementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScholarshipPrograms::route('/'),
            'create' => CreateScholarshipProgram::route('/create'),
            'view' => ViewScholarshipProgram::route('/{record}'),
            'edit' => EditScholarshipProgram::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['scholarshipApplications']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'description',
            'semester',
            'academic_year',
            'school_type_eligibility',
        ];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        $details = [];

        $details['Budget'] = '₱' . number_format($record->per_student_budget, 2) . ' per student';
        $details['Eligibility'] = match ($record->school_type_eligibility) {
            'high_school' => 'High School',
            'college' => 'College',
            'both' => 'All Students',
            default => ucfirst($record->school_type_eligibility),
        };
        $details['Deadline'] = $record->application_deadline->format('M j, Y');
        $details['Status'] = $record->active ? 'Active' : 'Inactive';

        return $details;
    }
}
