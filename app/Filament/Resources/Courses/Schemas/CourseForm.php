<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

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
                    ->label('العنوان (عربي)')
                    ->required()
                    ->live(debounce: 400)
                    ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('title_en')
                    ->label('العنوان (English)'),
                TextInput::make('slug')
                    ->label('الرابط (Slug)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->alphaDash()
                    ->readOnly()
                    ->helperText('يُنشأ تلقائياً من عنوان الدورة ويجب أن يكون فريداً'),
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
