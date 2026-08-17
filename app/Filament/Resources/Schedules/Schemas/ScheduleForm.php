<?php

namespace App\Filament\Resources\Schedules\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->relationship('course', 'id')
                    ->required(),
                TextInput::make('title_ar')
                    ->required(),
                Textarea::make('description_ar')
                    ->columnSpanFull(),
                Select::make('type')
                    ->options(['live' => 'Live', 'deadline' => 'Deadline', 'exam' => 'Exam'])
                    ->default('live')
                    ->required(),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at'),
                TextInput::make('meeting_url')
                    ->url(),
                Toggle::make('is_published')
                    ->required(),
            ]);
    }
}
