<?php

namespace App\Filament\Resources\Semesters\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GeneralCurriculumRelationManager extends RelationManager
{
    protected static string $relationship = 'curriculumAssignments';

    protected static ?string $title = 'المواد (منهج عام)';

    protected static ?string $modelLabel = 'مادة';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('subject_id')
                ->label('المادة')
                ->relationship('subject', 'name_ar', fn ($q) => $q->where('is_active', true))
                ->required()
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name_ar')->label('اسم المادة (عربي)')->required(),
                    TextInput::make('slug')->label('الرابط')->required(),
                    Toggle::make('is_active')->label('نشط')->default(true),
                ]),
            TextInput::make('sort_order')->label('الترتيب')->numeric()->default(0)->required(),
            Toggle::make('is_required')->label('مادة إلزامية')->default(true),
            Toggle::make('is_active')->label('نشط')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->whereNull('specialization_id'))
            ->columns([
                TextColumn::make('subject.name_ar')->label('المادة')->searchable(),
                TextColumn::make('sort_order')->label('الترتيب')->sortable(),
                ToggleColumn::make('is_required')->label('إلزامية'),
                ToggleColumn::make('is_active')->label('نشط'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make()->label('إضافة مادة'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['semester_id'] = $this->getOwnerRecord()->getKey();
        $data['specialization_id'] = null;

        return $data;
    }
}
