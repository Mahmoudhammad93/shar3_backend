<?php

namespace App\Filament\Resources\Lessons\Schemas;

use App\Enums\LessonBunnyStatus;
use App\Enums\LessonMediaType;
use App\Models\Lesson;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components(self::components(includeCourse: true));
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    public static function components(bool $includeCourse = true): array
    {
        $components = [];

        if ($includeCourse) {
            $components[] = Select::make('course_id')
                ->label('الدورة')
                ->relationship('course', 'title_ar')
                ->searchable()
                ->preload()
                ->required();
        }

        $components = array_merge($components, [
            TextInput::make('title_ar')
                ->label('العنوان (عربي)')
                ->required(),
            TextInput::make('title_en')
                ->label('العنوان (إنجليزي)'),
            Select::make('media_type')
                ->label('نوع الوسائط')
                ->options([
                    LessonMediaType::Video->value => 'فيديو',
                    LessonMediaType::Audio->value => 'صوت',
                    LessonMediaType::Text->value => 'نص',
                ])
                ->default(fn (?Lesson $record): ?string => $record === null
                    ? LessonMediaType::Text->value
                    : $record->media_type)
                ->placeholder('بدون تحديد — يبقى السلوك الحالي')
                ->required(fn (?Lesson $record): bool => $record === null)
                ->live()
                ->native(false),
            Section::make('فيديو Bunny Stream')
                ->schema([
                    ViewField::make('bunny_uploader')
                        ->view('filament.forms.components.bunny-video-uploader')
                        ->dehydrated(false),
                    Hidden::make('bunny_video_id'),
                    Hidden::make('bunny_library_id'),
                    Hidden::make('bunny_status'),
                    Hidden::make('video_provider'),
                ])
                ->visible(fn (Get $get): bool => $get('media_type') === LessonMediaType::Video->value)
                ->columnSpanFull(),
            Section::make('صوت (Google Drive)')
                ->schema([
                    TextInput::make('google_drive_file_id')
                        ->label('Google Drive File ID')
                        ->required(fn (Get $get): bool => $get('media_type') === LessonMediaType::Audio->value)
                        ->maxLength(255),
                    TextInput::make('google_drive_resource_key')
                        ->label('Google Drive Resource Key (اختياري)')
                        ->maxLength(255)
                        ->helperText('مطلوب لبعض الملفات المشاركة برابط محمي.'),
                ])
                ->visible(fn (Get $get): bool => $get('media_type') === LessonMediaType::Audio->value)
                ->columnSpanFull(),
            Section::make('رابط فيديو قديم')
                ->description('للدروس القديمة فقط. لا يُستبدل تلقائياً عند رفع فيديو Bunny.')
                ->schema([
                    TextInput::make('video_url')
                        ->label('الرابط')
                        ->url(),
                ])
                ->collapsed(fn (?Lesson $record): bool => blank($record?->video_url))
                ->visible(fn (Get $get): bool => $get('media_type') === LessonMediaType::Video->value)
                ->columnSpanFull(),
            Textarea::make('content_ar')
                ->label('المحتوى (عربي)')
                ->columnSpanFull(),
            Textarea::make('content_en')
                ->label('المحتوى (إنجليزي)')
                ->columnSpanFull(),
            TextInput::make('duration_minutes')
                ->label('المدة (دقائق)')
                ->numeric(),
            self::sortOrderField($includeCourse),
            Toggle::make('is_published')
                ->label('منشور')
                ->required(),
        ]);

        return $components;
    }

    private static function sortOrderField(bool $includeCourse): TextInput
    {
        $field = TextInput::make('sort_order')
            ->label('الترتيب')
            ->required()
            ->numeric()
            ->default(0)
            ->validationMessages([
                'unique' => 'رقم الترتيب مستخدم بالفعل في هذه الدورة.',
            ]);

        if ($includeCourse) {
            return $field->scopedUnique(
                modifyQueryUsing: function ($query, $component) {
                    $courseId = $component->getContainer()->getState()['course_id'] ?? null;

                    if ($courseId) {
                        $query->where('course_id', $courseId);
                    }

                    return $query;
                },
            );
        }

        return $field;
    }
}
