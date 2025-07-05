<?php

namespace App\Filament\Resources\StudentProfiles;

use App\Filament\Resources\StudentProfiles\Pages\CreateStudentProfile;
use App\Filament\Resources\StudentProfiles\Pages\EditStudentProfile;
use App\Filament\Resources\StudentProfiles\Pages\ListStudentProfiles;
use App\Filament\Resources\StudentProfiles\Pages\ViewStudentProfile;
use App\Filament\Resources\StudentProfiles\Schemas\StudentProfileForm;
use App\Filament\Resources\StudentProfiles\Schemas\StudentProfileInfolist;
use App\Filament\Resources\StudentProfiles\Tables\StudentProfilesTable;
use App\Models\StudentProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentProfileResource extends Resource
{
    protected static ?string $model = StudentProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Student Profiles';

    protected static ?string $modelLabel = 'Student Profile';

    protected static ?string $pluralModelLabel = 'Student Profiles';

    protected static ?int $navigationSort = 2;

    // protected static ?string $navigationGroup = 'Student Management';

    public static function form(Schema $schema): Schema
    {
        return StudentProfileForm::configure($schema);
    }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return StudentProfileInfolist::configure($schema);
    // }

    public static function table(Table $table): Table
    {
        return StudentProfilesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user']) // Eager load user relationship for better performance
            ->orderBy('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Could add scholarship applications relation here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentProfiles::route('/'),
            'create' => CreateStudentProfile::route('/create'),
            'view' => ViewStudentProfile::route('/{record}'),
            'edit' => EditStudentProfile::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['user']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'student_id',
            'school_name',
            'user.name',
            'user.email',
        ];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        $details = [];

        if ($record->user) {
            $details['User'] = $record->user->name;
        } else {
            $details['Name'] = trim($record->first_name . ' ' . $record->last_name);
        }

        $details['School'] = $record->school_name;
        $details['Type'] = ucfirst(str_replace('_', ' ', $record->school_type));
        $details['Status'] = ucfirst($record->status);

        return $details;
    }
}
