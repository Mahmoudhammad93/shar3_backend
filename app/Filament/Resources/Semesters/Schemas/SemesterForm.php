<?php

namespace App\Filament\Resources\Semesters\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SemesterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_year_id')
                    ->label('السنة الدراسية')
                    ->relationship('year', 'name_ar')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name_ar')->label('الاسم بالعربية')->required(),
                TextInput::make('name_en')->label('الاسم بالإنجليزية'),
                TextInput::make('slug')->label('الرابط المختصر')->required(),
                TextInput::make('semester_number')->label('رقم الفصل')->required()->numeric()->minValue(1),
                DatePicker::make('starts_at')->label('تاريخ البدء'),
                DatePicker::make('ends_at')->label('تاريخ الانتهاء'),
                TextInput::make('sort_order')->label('الترتيب')->required()->numeric()->default(0),
                Toggle::make('is_active')->label('نشط')->default(true),
            ]);
    }
}
