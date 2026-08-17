<?php

namespace App\Filament\Resources\Certificates\Schemas;

use App\Models\Certificate;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('الطالب')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('course_id')
                    ->label('الدورة')
                    ->relationship('course', 'title_ar')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('certificate_number')
                    ->label('رقم الشهادة')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn (): string => self::nextCertificateNumber())
                    ->helperText('يُولَّد تلقائياً عند الإنشاء ويمكن تعديله'),
                DateTimePicker::make('issued_at')
                    ->label('تاريخ الإصدار')
                    ->required()
                    ->default(now()),
                TextInput::make('file_path')
                    ->label('مسار الملف'),
            ]);
    }

    public static function nextCertificateNumber(): string
    {
        $next = (Certificate::query()->max('id') ?? 0) + 1;

        return 'SHR3-'.str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
