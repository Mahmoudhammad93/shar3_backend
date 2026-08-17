<?php

namespace App\Filament\Resources\Lessons\RelationManagers;

use App\Models\LessonQuestion;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $title = 'أسئلة الدرس';

    protected static ?string $modelLabel = 'سؤال';

    protected static ?string $pluralModelLabel = 'الأسئلة';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('question_ar')
                    ->label('السؤال')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('نوع السؤال')
                    ->options(LessonQuestion::typeOptions())
                    ->default(LessonQuestion::TYPE_CHOICE)
                    ->required()
                    ->live()
                    ->native(false),
                Select::make('correct_answer')
                    ->label('الإجابة الصحيحة')
                    ->options([
                        'true' => 'صح',
                        'false' => 'خطأ',
                    ])
                    ->visible(fn (Get $get): bool => $get('type') === LessonQuestion::TYPE_TRUE_FALSE)
                    ->required(fn (Get $get): bool => $get('type') === LessonQuestion::TYPE_TRUE_FALSE),
                TextInput::make('correct_answer')
                    ->label('الإجابة الصحيحة (كتابة)')
                    ->visible(fn (Get $get): bool => $get('type') === LessonQuestion::TYPE_TEXT)
                    ->required(fn (Get $get): bool => $get('type') === LessonQuestion::TYPE_TEXT)
                    ->helperText('يُقارَن مع إجابة الطالب دون حساسية للحروف الكبيرة/الصغيرة'),
                TextInput::make('sort_order')
                    ->label('الترتيب')
                    ->numeric()
                    ->default(0),
                Repeater::make('options')
                    ->label('الاختيارات')
                    ->relationship()
                    ->schema([
                        TextInput::make('option_ar')
                            ->label('الاختيار')
                            ->required(),
                        Toggle::make('is_correct')
                            ->label('إجابة صحيحة')
                            ->default(false),
                        TextInput::make('sort_order')
                            ->label('الترتيب')
                            ->numeric()
                            ->default(0),
                    ])
                    ->minItems(2)
                    ->defaultItems(2)
                    ->visible(fn (Get $get): bool => $get('type') === LessonQuestion::TYPE_CHOICE)
                    ->required(fn (Get $get): bool => $get('type') === LessonQuestion::TYPE_CHOICE)
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->recordTitleAttribute('question_ar')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#'),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => LessonQuestion::typeOptions()[$state] ?? $state),
                TextColumn::make('question_ar')
                    ->label('السؤال')
                    ->wrap(),
                TextColumn::make('options_count')
                    ->label('الاختيارات')
                    ->counts('options'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
