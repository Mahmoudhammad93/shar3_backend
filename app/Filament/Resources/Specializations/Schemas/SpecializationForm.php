<?php

namespace App\Filament\Resources\Specializations\Schemas;

use App\Enums\CurriculumType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SpecializationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_level_id')
                    ->label('المستوى الأكاديمي')
                    ->relationship(
                        'level',
                        'name_ar',
                        fn (Builder $query) => $query
                            ->where('curriculum_type', CurriculumType::Specialized->value)
                            ->where('is_active', true)
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('يُسمح فقط بالمستوى المتخصص'),
                TextInput::make('name_ar')->label('التخصص (عربي)')->required(),
                TextInput::make('name_en')->label('التخصص (English)'),
                TextInput::make('slug')->label('الرابط')->required(),
                Textarea::make('description_ar')->label('الوصف')->columnSpanFull(),
                TextInput::make('sort_order')->label('الترتيب')->required()->numeric()->default(0),
                Toggle::make('is_active')->label('نشط')->default(true),
            ]);
    }
}
