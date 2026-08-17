<?php

namespace App\Filament\Resources\AcademicLevels\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicLevelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_ar')->label('الاسم (عربي)')->required(),
                TextInput::make('name_en')->label('الاسم (English)'),
                TextInput::make('slug')->label('الرابط')->required(),
                TextInput::make('number')->label('رقم المستوى')->required()->numeric()->minValue(1),
                Textarea::make('description_ar')->label('الوصف (عربي)')->columnSpanFull(),
                Textarea::make('description_en')->label('الوصف (English)')->columnSpanFull(),
                TextInput::make('sort_order')->label('الترتيب')->required()->numeric()->default(0),
                Toggle::make('is_active')->label('نشط')->default(true),
            ]);
    }
}
