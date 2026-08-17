<?php

namespace App\Filament\Resources\Lessons\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LessonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->label('الدورة')
                    ->relationship('course', 'title_ar')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title_ar')
                    ->label('العنوان (عربي)')
                    ->required(),
                TextInput::make('title_en')
                    ->label('العنوان (إنجليزي)'),
                Textarea::make('content_ar')
                    ->label('المحتوى (عربي)')
                    ->columnSpanFull(),
                Textarea::make('content_en')
                    ->label('المحتوى (إنجليزي)')
                    ->columnSpanFull(),
                TextInput::make('video_url')
                    ->label('رابط الفيديو')
                    ->url(),
                TextInput::make('duration_minutes')
                    ->label('المدة (دقائق)')
                    ->numeric(),
                TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->scopedUnique(
                        modifyQueryUsing: function ($query, $component) {
                            $courseId = $component->getContainer()->getState()['course_id'] ?? null;

                            if ($courseId) {
                                $query->where('course_id', $courseId);
                            }

                            return $query;
                        },
                    )
                    ->validationMessages([
                        'unique' => 'رقم الترتيب مستخدم بالفعل في هذه الدورة.',
                    ]),
                Toggle::make('is_published')
                    ->label('منشور')
                    ->required(),
            ]);
    }
}
