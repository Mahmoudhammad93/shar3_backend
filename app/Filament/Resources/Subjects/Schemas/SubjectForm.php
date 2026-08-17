<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_ar')->label('اسم المادة (عربي)')->required(),
                TextInput::make('name_en')->label('اسم المادة (English)'),
                TextInput::make('slug')->label('الرابط')->required()->unique(ignoreRecord: true),
                Textarea::make('description_ar')->label('الوصف')->columnSpanFull(),
                Select::make('course_id')
                    ->label('ربط بدورة موجودة')
                    ->relationship('course', 'title_ar')
                    ->searchable()
                    ->preload()
                    ->helperText('اختياري — يربط المادة بدورة ودروسها الحالية'),
                Section::make('محتوى الخطة الدراسية')->schema([
                    Textarea::make('memorization_ar')
                        ->label('الحفظ')
                        ->rows(3)
                        ->helperText('المتون أو الأجزاء المطلوب حفظها')
                        ->columnSpanFull(),
                    Textarea::make('primary_text_ar')
                        ->label('المتون / الكتب الأساسية')
                        ->rows(4)
                        ->helperText('يظهر في عمود «أساسي» في الموقع')
                        ->columnSpanFull(),
                    Textarea::make('supplementary_text_ar')
                        ->label('الكتب التكميلية')
                        ->rows(4)
                        ->helperText('يظهر في عمود «تكميلي» في الموقع')
                        ->columnSpanFull(),
                ])->columnSpanFull(),
                Toggle::make('is_active')->label('نشط')->default(true),
            ]);
    }
}
