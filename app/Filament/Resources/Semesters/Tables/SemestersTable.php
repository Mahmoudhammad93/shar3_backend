<?php

namespace App\Filament\Resources\Semesters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SemestersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year.level.name_ar')->label('المستوى'),
                TextColumn::make('year.name_ar')->label('السنة')->sortable(),
                TextColumn::make('name_ar')->label('الفصل')->searchable(),
                TextColumn::make('semester_number')->label('الرقم')->sortable(),
                TextColumn::make('curriculum_assignments_count')->label('المقررات')->counts('curriculumAssignments'),
                IconColumn::make('is_active')->label('نشط')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
