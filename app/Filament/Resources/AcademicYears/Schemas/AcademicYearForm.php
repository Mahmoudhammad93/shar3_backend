<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_level_id')
                    ->label('المستوى')
                    ->relationship('level', 'name_ar')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name_ar')->label('الاسم (عربي)')->required(),
                TextInput::make('name_en')->label('الاسم (English)'),
                TextInput::make('slug')->label('الرابط')->required(),
                TextInput::make('year_number')->label('رقم السنة')->required()->numeric()->minValue(0),
                Textarea::make('description_ar')->label('الوصف')->columnSpanFull(),
                TextInput::make('sort_order')->label('الترتيب')->required()->numeric()->default(0),
                Toggle::make('is_active')->label('نشط')->default(true),
            ]);
    }
}
