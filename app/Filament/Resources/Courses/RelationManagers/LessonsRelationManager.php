<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\Lessons\LessonResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    protected static ?string $title = 'دروس الدورة';

    protected static ?string $modelLabel = 'درس';

    protected static ?string $pluralModelLabel = 'الدروس';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_ar')
                    ->label('العنوان (عربي)')
                    ->required(),
                TextInput::make('title_en')
                    ->label('العنوان (إنجليزي)'),
                Textarea::make('content_ar')
                    ->label('المحتوى')
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
                    ->rules(function () {
                        $courseId = $this->getOwnerRecord()->getKey();

                        return [
                            Rule::unique('lessons', 'sort_order')
                                ->where('course_id', $courseId)
                                ->ignore($this->getMountedTableActionRecord()),
                        ];
                    })
                    ->validationMessages([
                        'unique' => 'رقم الترتيب مستخدم بالفعل في هذه الدورة.',
                    ]),
                Toggle::make('is_published')
                    ->label('منشور')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->recordTitleAttribute('title_ar')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('title_ar')
                    ->label('العنوان')
                    ->searchable(),
                TextColumn::make('duration_minutes')
                    ->label('الدقائق'),
                TextColumn::make('questions_count')
                    ->label('أسئلة')
                    ->counts('questions'),
                IconColumn::make('is_published')
                    ->label('منشور')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn ($record) => LessonResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make(),
            ]);
    }
}
