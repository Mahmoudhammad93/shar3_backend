<?php

namespace App\Filament\Resources\Specializations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SpecializationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('level.name_ar')->label('المستوى'),
                TextColumn::make('name_ar')->label('التخصص')->searchable()->sortable(),
                TextColumn::make('subjects_count')->label('المواد')->counts('subjects'),
                IconColumn::make('is_active')->label('نشط')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
