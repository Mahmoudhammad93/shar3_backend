<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Models\Student;
use App\Support\Countries;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('الاسم')
                    ->required(),
                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('الجوال')
                    ->tel(),
                Select::make('gender')
                    ->label('الجنس')
                    ->options(Student::genderOptions())
                    ->native(false),
                DatePicker::make('birth_date')
                    ->label('تاريخ الميلاد'),
                Select::make('country')
                    ->label('الدولة')
                    ->options(Countries::options())
                    ->getOptionLabelUsing(fn (?string $value): ?string => Countries::displayName($value))
                    ->searchable()
                    ->native(false),
                TextInput::make('city')
                    ->label('المدينة'),
                TextInput::make('national_id')
                    ->label('رقم الهوية'),
                Select::make('status')
                    ->label('الحالة')
                    ->options(Student::statusOptions())
                    ->required()
                    ->default(Student::STATUS_PENDING)
                    ->native(false),
                Textarea::make('notes')
                    ->label('ملاحظات')
                    ->columnSpanFull(),
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
                    ->helperText('ارفع صورة — ستظهر معاينة مباشرة بعد الاختيار'),
            ]);
    }
}
