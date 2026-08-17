<?php

namespace App\Filament\Resources\Assignments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AssignmentForm
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
                TextInput::make('title_en'),
                Textarea::make('description_ar')
                    ->columnSpanFull(),
                DateTimePicker::make('due_at'),
                TextInput::make('max_score')
                    ->required()
                    ->numeric()
                    ->default(100),
                Toggle::make('is_published')
                    ->required(),
            ]);
    }
}
