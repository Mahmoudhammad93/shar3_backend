<?php

namespace App\Filament\Resources\AcademicLevels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AcademicLevelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_ar')->label('المستوى')->searchable()->sortable(),
                TextColumn::make('number')->label('الرقم')->sortable(),
                TextColumn::make('years_count')->label('السنوات')->counts('years'),
                TextColumn::make('sort_order')->label('الترتيب')->sortable(),
                IconColumn::make('is_active')->label('نشط')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
