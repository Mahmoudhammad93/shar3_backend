<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        TextInput::make('name_ar')
                            ->label('الاسم (عربي)')
                            ->required()
                            ->maxLength(255)
                            ->live(debounce: 400)
                            ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('name_en')
                            ->label('الاسم (English)')
                            ->maxLength(255),
                        TextInput::make('title_ar')
                            ->label('اللقب / المسمى (عربي)')
                            ->maxLength(255)
                            ->placeholder('مثال: أستاذ الفقه'),
                        TextInput::make('title_en')
                            ->label('اللقب / المسمى (English)')
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('الرابط (Slug)')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->alphaDash()
                            ->readOnly()
                            ->helperText('يُنشأ تلقائياً من الاسم العربي')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('الصورة والتخصص')
                    ->schema([
                        FileUpload::make('photo')
                            ->label('الصورة الشخصية')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('teachers')
                            ->visibility('public')
                            ->imagePreviewHeight('220')
                            ->imageEditor()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('اسحب الصورة هنا أو انقر للرفع — ستظهر معاينة مباشرة')
                            ->columnSpan(1),
                        TextInput::make('specializations')
                            ->label('التخصصات')
                            ->maxLength(255)
                            ->placeholder('الفقه، أصول الفقه')
                            ->helperText('افصل بين التخصصات بفاصلة — تظهر في صفحة المعلم')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('السيرة الذاتية')
                    ->schema([
                        Textarea::make('bio_ar')
                            ->label('نبذة (عربي)')
                            ->rows(5)
                            ->columnSpanFull(),
                        Textarea::make('bio_en')
                            ->label('نبذة (English)')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('التواصل')
                    ->schema([
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('الجوال')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('الإعدادات')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->helperText('يظهر في الموقع ويُحسب ضمن إحصائيات المعلمين'),
                        Toggle::make('is_featured')
                            ->label('مميز في الصفحة الرئيسية')
                            ->default(false)
                            ->helperText('يظهر في قسم المعلمين بالصفحة الرئيسية (حتى 4)'),
                        TextInput::make('sort_order')
                            ->label('ترتيب العرض')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('الأرقام الأصغر تظهر أولاً'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
