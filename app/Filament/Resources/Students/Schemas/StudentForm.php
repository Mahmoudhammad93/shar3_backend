<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Student;
use App\Support\Countries;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components(self::components());
    }

    /** @return array<int, \Filament\Forms\Components\Component|\Filament\Schemas\Components\Component> */
    public static function components(): array
    {
        return [
            Section::make('البيانات الشخصية')
                ->schema([
                    TextInput::make('first_name')
                        ->label('الاسم الأول')
                        ->required()
                        ->maxLength(255)
                        ->live(debounce: 400)
                        ->afterStateUpdated(fn (Set $set, ?string $state, Get $get): mixed => $set(
                            'name',
                            trim(($state ?? '').' '.($get('last_name') ?? '')),
                        )),
                    TextInput::make('last_name')
                        ->label('الاسم الأخير')
                        ->required()
                        ->maxLength(255)
                        ->live(debounce: 400)
                        ->afterStateUpdated(fn (Set $set, ?string $state, Get $get): mixed => $set(
                            'name',
                            trim(($get('first_name') ?? '').' '.($state ?? '')),
                        )),
                    TextInput::make('name')
                        ->label('الاسم الكامل')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Select::make('gender')
                        ->label('الجنس')
                        ->options(Student::genderOptions())
                        ->native(false),
                    DatePicker::make('birth_date')
                        ->label('تاريخ الميلاد'),
                    TextInput::make('national_id')
                        ->label('رقم الهوية'),
                    FileUpload::make('photo')
                        ->label('الصورة الشخصية')
                        ->image()
                        ->avatar()
                        ->disk('public')
                        ->directory('students')
                        ->visibility('public')
                        ->imagePreviewHeight('200')
                        ->imageEditor()
                        ->maxSize(5120)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('التواصل')
                ->schema([
                    TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->label('الجوال')
                        ->tel()
                        ->maxLength(50),
                    TextInput::make('whatsapp')
                        ->label('واتساب')
                        ->tel()
                        ->maxLength(50),
                    Select::make('country')
                        ->label('الدولة')
                        ->options(Countries::options())
                        ->getOptionLabelUsing(fn (?string $value): ?string => Countries::displayName($value))
                        ->searchable()
                        ->native(false),
                    Select::make('nationality')
                        ->label('الجنسية')
                        ->options(Countries::options())
                        ->getOptionLabelUsing(fn (?string $value): ?string => Countries::displayName($value))
                        ->searchable()
                        ->native(false),
                    TextInput::make('city')
                        ->label('المدينة')
                        ->maxLength(255),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('بيانات التسجيل')
                ->schema([
                    Select::make('education_level')
                        ->label('المؤهل العلمي')
                        ->options(Student::educationLevelOptions())
                        ->native(false),
                    Select::make('heard_about')
                        ->label('كيف عرفت عن المعهد؟')
                        ->options(Student::heardAboutOptions())
                        ->native(false),
                    Select::make('daily_hours')
                        ->label('ساعات الدراسة اليومية')
                        ->options(Student::dailyHoursOptions())
                        ->native(false),
                    Toggle::make('works_full_time')
                        ->label('يعمل بدوام كامل')
                        ->inline(false),
                    Toggle::make('participates_other_programs')
                        ->label('يشارك في برامج أخرى')
                        ->inline(false),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('التسكين الأكاديمي')
                ->schema([
                    Select::make('academic_level_id')
                        ->label('المستوى الأكاديمي')
                        ->options(fn () => AcademicLevel::query()
                            ->where('is_active', true)
                            ->orderBy('number')
                            ->pluck('name_ar', 'id'))
                        ->searchable()
                        ->preload()
                        ->live()
                        ->native(false),
                    Select::make('academic_year_id')
                        ->label('السنة الدراسية')
                        ->options(fn (Get $get) => AcademicYear::query()
                            ->where('is_active', true)
                            ->when(
                                $get('academic_level_id'),
                                fn (Builder $query, $levelId) => $query->where('academic_level_id', $levelId),
                            )
                            ->orderBy('year_number')
                            ->pluck('name_ar', 'id'))
                        ->searchable()
                        ->preload()
                        ->live()
                        ->native(false),
                    Select::make('current_semester_id')
                        ->label('الفصل الحالي')
                        ->options(fn (Get $get) => Semester::query()
                            ->where('is_active', true)
                            ->when(
                                $get('academic_year_id'),
                                fn (Builder $query, $yearId) => $query->where('academic_year_id', $yearId),
                            )
                            ->orderBy('semester_number')
                            ->pluck('name_ar', 'id'))
                        ->searchable()
                        ->preload()
                        ->native(false),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('إعدادات الحساب')
                ->schema([
                    Select::make('status')
                        ->label('الحالة')
                        ->options(Student::statusOptions())
                        ->required()
                        ->default(Student::STATUS_ACTIVE)
                        ->native(false),
                    Textarea::make('notes')
                        ->label('ملاحظات')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ];
    }

    public static function normalizeCountry(?string $country): ?string
    {
        return filled($country) ? Countries::resolveCode($country) : null;
    }

    /** @param  array<string, mixed>  $data */
    public static function prepareForFill(array $data): array
    {
        if (! empty($data['country'])) {
            $data['country'] = self::normalizeCountry($data['country']);
        }

        if (! empty($data['nationality'])) {
            $data['nationality'] = self::normalizeCountry($data['nationality']);
        }

        if (empty($data['first_name']) && empty($data['last_name']) && ! empty($data['name'])) {
            $parts = preg_split('/\s+/u', trim($data['name']), 2) ?: [];
            $data['first_name'] = $parts[0] ?? '';
            $data['last_name'] = $parts[1] ?? '';
        }

        return $data;
    }
}
