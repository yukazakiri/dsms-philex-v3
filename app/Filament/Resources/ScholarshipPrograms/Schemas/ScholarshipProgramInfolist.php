<?php

namespace App\Filament\Resources\ScholarshipPrograms\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScholarshipProgramInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ===== PROGRAM HEADER SECTION =====
                Section::make('Program Overview')
                    ->columnSpanFull()
                    ->schema([
  TextEntry::make('Overview')
                    ->label('')
                    ->formatStateUsing(fn () => '🎓 SCHOLARSHIP PROGRAM OVERVIEW')
                    ->size('xl')
                    ->weight('bold')
                    ->color('primary')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'text-center bg-blue-50 p-4 rounded-lg border-2 border-blue-200 mb-6']),

                TextEntry::make('name')
                    ->label('')
                    ->size('xl')
                    ->weight('bold')
                    ->color('primary')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'text-center text-2xl']),

                TextEntry::make('description')
                    ->label('📝 Program Description')
                    ->prose()
                    ->markdown()
                    ->columnSpanFull()
                    ->color('gray')
                    ->extraAttributes(['class' => 'bg-gray-50 p-4 rounded-lg border']),

                // ===== STATUS & METRICS SECTION =====
                TextEntry::make('Program')
                    ->label('')
                    ->formatStateUsing(fn () => '📊 PROGRAM STATUS & METRICS')
                    ->size('lg')
                    ->weight('bold')
                    ->color('info')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'text-center bg-blue-50 p-3 rounded-lg mt-6 mb-4']),

                TextEntry::make('active')
                    ->label('🔄 Program Status')
                    ->badge()
                    ->size('xl')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? '🟢 ACTIVE' : '🔴 INACTIVE')
                    ->weight('bold')
                    ->extraAttributes(['class' => 'text-center']),

                TextEntry::make('application_deadline')
                    ->label('⏰ Application Deadline')
                    ->date('F j, Y')
                    ->size('xl')
                    ->weight('bold')
                    ->color(fn ($record) => $record->application_deadline->isPast() ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->application_deadline->isPast()
                            ? '🚫 CLOSED - ' . $state
                            : '✅ OPEN - ' . $state
                    )
                    ->helperText(fn ($record) =>
                        $record->application_deadline->isPast()
                            ? 'Applications are no longer accepted'
                            : 'Applications are still being accepted'
                    )
                    ->extraAttributes(['class' => 'text-center']),

                TextEntry::make('scholarship_applications_count')
                    ->label('📊 Application Statistics')
                    ->counts('scholarshipApplications')
                    ->badge()
                    ->size('xl')
                    ->color('info')
                    ->formatStateUsing(fn ($state, $record) =>
                        $state . ' / ' . $record->available_slots . ' applications'
                    )
                    ->helperText(fn ($record) =>
                        'Slots remaining: ' . max(0, $record->available_slots - $record->scholarshipApplications->count())
                    )
                    ->extraAttributes(['class' => 'text-center']),

                    ]),
              
                // ===== FINANCIAL OVERVIEW SECTION =====
                TextEntry::make('Finanical OverView')
                    ->label('')
                    ->formatStateUsing(fn () => '💰 FINANCIAL OVERVIEW')
                    ->size('lg')
                    ->weight('bold')
                    ->color('success')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'text-center bg-green-50 p-3 rounded-lg mt-6 mb-4']),

                TextEntry::make('total_budget')
                    ->label('💰 Total Program Budget')
                    ->formatStateUsing(fn ($state) => '₱' . number_format($state, 2))
                    ->size('xl')
                    ->weight('bold')
                    ->color('success')
                    ->copyable()
                    ->copyMessage('Budget amount copied!')
                    ->helperText('Total funding allocated for this program')
                    ->extraAttributes(['class' => 'text-center bg-green-100 p-3 rounded']),

                TextEntry::make('per_student_budget')
                    ->label('💵 Award per Student')
                    ->formatStateUsing(fn ($state) => '₱' . number_format($state, 2))
                    ->size('xl')
                    ->weight('bold')
                    ->color('warning')
                    ->copyable()
                    ->copyMessage('Award amount copied!')
                    ->helperText('Amount each student will receive')
                    ->extraAttributes(['class' => 'text-center bg-yellow-100 p-3 rounded']),

                TextEntry::make('available_slots')
                    ->label('👥 Available Scholarships')
                    ->numeric()
                    ->size('xl')
                    ->weight('bold')
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state . ' scholarships')
                    ->helperText('Total number of scholarships available')
                    ->extraAttributes(['class' => 'text-center bg-blue-100 p-3 rounded']),

                // ===== ACADEMIC REQUIREMENTS SECTION =====
                TextEntry::make('Academic Requirements')
                    ->label('')
                    ->formatStateUsing(fn () => '🎓 ACADEMIC REQUIREMENTS')
                    ->size('lg')
                    ->weight('bold')
                    ->color('primary')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'text-center bg-blue-50 p-3 rounded-lg mt-6 mb-4']),

                TextEntry::make('semester')
                    ->label('📅 Target Semester')
                    ->badge()
                    ->color('info')
                    ->size('lg')
                    ->extraAttributes(['class' => 'text-center']),

                TextEntry::make('academic_year')
                    ->label('🗓️ Academic Year')
                    ->badge()
                    ->color('info')
                    ->size('lg')
                    ->extraAttributes(['class' => 'text-center']),

                TextEntry::make('school_type_eligibility')
                    ->label('� Eligible Students')
                    ->badge()
                    ->size('xl')
                    ->color(fn (string $state): string => match ($state) {
                        'high_school' => 'blue',
                        'college' => 'green',
                        'both' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'high_school' => '🏫 HIGH SCHOOL STUDENTS',
                        'college' => '🏛️ COLLEGE STUDENTS',
                        'both' => '📚 ALL STUDENTS',
                        default => strtoupper($state),
                    })
                    ->extraAttributes(['class' => 'text-center']),

                // ===== ELIGIBILITY CRITERIA SECTION =====
                TextEntry::make('Eligibility Criteria')
                    ->label('')
                    ->formatStateUsing(fn () => '✅ ELIGIBILITY CRITERIA')
                    ->size('lg')
                    ->weight('bold')
                    ->color('warning')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'text-center bg-yellow-50 p-3 rounded-lg mt-6 mb-4']),

                TextEntry::make('min_gpa')
                    ->label('📊 Minimum GPA')
                    ->formatStateUsing(fn ($state) => '⭐ ' . number_format($state, 2) . '%')
                    ->badge()
                    ->color('warning')
                    ->size('xl')
                    ->helperText('Students must maintain this GPA or higher')
                    ->extraAttributes(['class' => 'text-center bg-yellow-100 p-3 rounded']),

                TextEntry::make('min_units')
                    ->label('📚 Minimum Units')
                    ->formatStateUsing(fn ($state) => '📖 ' . $state . ' units')
                    ->badge()
                    ->color('info')
                    ->size('xl')
                    ->visible(fn ($record) => $record->min_units !== null)
                    ->helperText('Required course load for college students')
                    ->extraAttributes(['class' => 'text-center bg-blue-100 p-3 rounded']),

                TextEntry::make('community_service_days')
                    ->label('🤝 Community Service')
                    ->formatStateUsing(fn ($state) => '❤️ ' . $state . ' days required')
                    ->badge()
                    ->color('pink')
                    ->size('xl')
                    ->helperText('Students must complete community service')
                    ->extraAttributes(['class' => 'text-center bg-pink-100 p-3 rounded']),

                // ===== DOCUMENT REQUIREMENTS SECTION =====
                TextEntry::make('Document Requirements')
                    ->label('')
                    ->formatStateUsing(fn () => '📋 DOCUMENT REQUIREMENTS')
                    ->size('lg')
                    ->weight('bold')
                    ->color('danger')
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record->documentRequirements && $record->documentRequirements->count() > 0)
                    ->extraAttributes(['class' => 'text-center bg-red-50 p-3 rounded-lg mt-6 mb-4']),

                RepeatableEntry::make('documentRequirements')
                    ->label('')
                    ->schema([
                        TextEntry::make('name')
                            ->label('')
                            ->weight('bold')
                            ->size('lg')
                            ->formatStateUsing(fn ($state) => '📄 ' . strtoupper($state))
                            ->color('primary')
                            ->extraAttributes(['class' => 'text-center']),

                        TextEntry::make('is_required')
                            ->label('')
                            ->badge()
                            ->size('xl')
                            ->color(fn (bool $state): string => $state ? 'danger' : 'warning')
                            ->formatStateUsing(fn (bool $state): string => $state ? '🔴 REQUIRED' : '⚪ OPTIONAL')
                            ->extraAttributes(['class' => 'text-center']),

                        TextEntry::make('description')
                            ->label('')
                            ->color('gray')
        
                            ->formatStateUsing(fn ($state) => '💡 ' . $state)
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'text-center bg-gray-100 p-2 rounded mt-2']),
                    ])
                    ->columns(2)
                    ->contained(false)
                    ->visible(fn ($record) => $record->documentRequirements && $record->documentRequirements->count() > 0)
                    ->columnSpanFull(),

                // ===== SYSTEM INFORMATION SECTION =====
                TextEntry::make('System Information')
                    ->label('')
                    ->formatStateUsing(fn () => '🔧 SYSTEM INFORMATION')
                    ->size('md')
                    ->weight('bold')
                    ->color('gray')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'text-center bg-gray-50 p-2 rounded-lg mt-8 mb-2']),

                TextEntry::make('created_at')
                    ->label('📅 Created')
                    ->dateTime('F j, Y \a\t g:i A')
                    ->color('gray')
                    ->size('sm')
                    ->icon('heroicon-o-plus-circle')
                    ->extraAttributes(['class' => 'text-center']),

                TextEntry::make('updated_at')
                    ->label('✏️ Last Updated')
                    ->dateTime('F j, Y \a\t g:i A')
                    ->color('gray')
                    ->size('sm')
                    ->icon('heroicon-o-pencil-square')
                    ->extraAttributes(['class' => 'text-center']),
            ])
            ->columns(3); // Use 3-column layout for better organization
    }
}
