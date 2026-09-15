<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\Lessons\LessonResource;
use App\Filament\Resources\Lessons\Schemas\LessonForm;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
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
        $components = array_values(array_filter(
            LessonForm::components(includeCourse: false),
            fn ($component) => ! in_array($component->getName(), ['sort_order', 'is_published'], true),
        ));

        $components[] = TextInput::make('sort_order')
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
            ]);

        $components[] = \Filament\Forms\Components\Toggle::make('is_published')
            ->label('منشور')
            ->default(true);

        return $schema->components($components);
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
                TextColumn::make('media_type')
                    ->label('الوسائط')
                    ->placeholder('—'),
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
