<?php

namespace App\Filament\Resources\AcademicLevels\Schemas;

use App\Enums\CurriculumType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AcademicLevelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_ar')->label('الاسم بالعربية')->required(),
                TextInput::make('name_en')->label('الاسم بالإنجليزية'),
                TextInput::make('slug')->label('الرابط المختصر')->required(),
                TextInput::make('number')->label('رقم المستوى')->required()->numeric()->minValue(1),
                Select::make('curriculum_type')
                    ->label('نوع المنهج')
                    ->options(collect(CurriculumType::cases())->mapWithKeys(
                        fn (CurriculumType $type) => [$type->value => $type->labelAr()]
                    ))
                    ->required()
                    ->default(CurriculumType::General->value)
                    ->helperText('العام: تمهيدي ومتقدم — المتخصص: يسمح باختيار التخصصات'),
                Textarea::make('description_ar')->label('الوصف بالعربية')->columnSpanFull(),
                Textarea::make('description_en')->label('الوصف بالإنجليزية')->columnSpanFull(),
                TextInput::make('sort_order')->label('الترتيب')->required()->numeric()->default(0),
                Toggle::make('is_active')->label('نشط')->default(true),
            ]);
    }
}
