<?php

namespace App\Filament\Resources\AcademicYears\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_level_id')
                    ->label('المستوى الأكاديمي')
                    ->relationship('level', 'name_ar')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name_ar')->label('الاسم بالعربية')->required(),
                TextInput::make('name_en')->label('الاسم بالإنجليزية'),
                TextInput::make('slug')->label('الرابط المختصر')->required(),
                TextInput::make('year_number')->label('رقم السنة')->required()->numeric()->minValue(0),
                Textarea::make('description_ar')->label('الوصف بالعربية')->columnSpanFull(),
                TextInput::make('sort_order')->label('الترتيب')->required()->numeric()->default(0),
                Toggle::make('is_active')->label('نشط')->default(true),
            ]);
    }
}
