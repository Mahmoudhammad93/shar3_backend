<?php

namespace App\Filament\Resources\Subjects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('semester.year.level.name_ar')->label('المستوى'),
                TextColumn::make('semester.year.name_ar')->label('السنة'),
                TextColumn::make('semester.name_ar')->label('الفصل'),
                TextColumn::make('subject.name_ar')->label('المادة')->searchable()->sortable(),
                TextColumn::make('subject.memorization_ar')->label('الحفظ')->limit(40)->placeholder('—')->toggleable(),
                TextColumn::make('subject.primary_text_ar')->label('أساسي')->limit(40)->placeholder('—')->toggleable(),
                TextColumn::make('subject.supplementary_text_ar')->label('تكميلي')->limit(40)->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('specialization.name_ar')->label('التخصص')->placeholder('—'),
                TextColumn::make('subject.course.title_ar')->label('الدورة المرتبطة')->placeholder('—'),
                IconColumn::make('is_required')->label('إلزامية')->boolean(),
                IconColumn::make('is_active')->label('نشط')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
