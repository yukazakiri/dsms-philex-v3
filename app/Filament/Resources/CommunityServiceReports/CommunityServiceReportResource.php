<?php

namespace App\Filament\Resources\CommunityServiceReports;

use App\Filament\Resources\CommunityServiceReports\Pages\CreateCommunityServiceReport;
use App\Filament\Resources\CommunityServiceReports\Pages\EditCommunityServiceReport;
use App\Filament\Resources\CommunityServiceReports\Pages\ListCommunityServiceReports;
use App\Filament\Resources\CommunityServiceReports\Pages\ViewCommunityServiceReport;
use App\Filament\Resources\CommunityServiceReports\Schemas\CommunityServiceReportForm;
use App\Filament\Resources\CommunityServiceReports\Schemas\CommunityServiceReportInfolist;
use App\Filament\Resources\CommunityServiceReports\Tables\CommunityServiceReportsTable;
use App\Models\CommunityServiceReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommunityServiceReportResource extends Resource
{
    protected static ?string $model = CommunityServiceReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CommunityServiceReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommunityServiceReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunityServiceReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommunityServiceReports::route('/'),
            'create' => CreateCommunityServiceReport::route('/create'),
            'view' => ViewCommunityServiceReport::route('/{record}'),
            'edit' => EditCommunityServiceReport::route('/{record}/edit'),
        ];
    }
}
