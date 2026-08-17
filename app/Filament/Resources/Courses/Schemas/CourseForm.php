<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('التصنيف')
                    ->relationship('category', 'name_ar')
                    ->searchable()
                    ->preload(),
                Select::make('program_id')
                    ->label('البرنامج')
                    ->relationship('program', 'name_ar')
                    ->searchable()
                    ->preload(),
                Select::make('teacher_id')
                    ->label('المعلم')
                    ->relationship('teacher', 'name_ar')
                    ->searchable()
                    ->preload(),
                TextInput::make('title_ar')
                    ->required(),
                TextInput::make('title_en'),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description_ar')
                    ->columnSpanFull(),
                Textarea::make('description_en')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('صورة الدورة')
                    ->image()
                    ->directory('courses')
                    ->imageEditor()
                    ->helperText('تظهر في بطاقات الدورات ولوحة الطالب'),
                TextInput::make('duration_hours')
                    ->numeric(),
                TextInput::make('level'),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                Toggle::make('is_free')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_published')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('start_date'),
                DatePicker::make('end_date'),
            ]);
    }
}
