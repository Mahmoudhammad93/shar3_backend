<?php

namespace App\Filament\Resources\Specializations\RelationManagers;

use App\Models\Semester;
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
use Illuminate\Database\Eloquent\Builder;

class CurriculumAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'curriculumAssignments';

    protected static ?string $title = 'منهج التخصص';

    protected static ?string $modelLabel = 'م assignment';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('semester_id')
                ->label('الفصل الدراسي')
                ->options(function () {
                    $levelId = $this->getOwnerRecord()->academic_level_id;

                    return Semester::query()
                        ->where('is_active', true)
                        ->whereHas('year', fn (Builder $q) => $q
                            ->where('academic_level_id', $levelId)
                            ->where('is_active', true))
                        ->with('year')
                        ->orderBy('sort_order')
                        ->get()
                        ->mapWithKeys(fn (Semester $semester) => [
                            $semester->id => ($semester->year?->name_ar ?? '').' — '.$semester->name_ar,
                        ]);
                })
                ->required()
                ->searchable()
                ->preload(),
            Select::make('subject_id')
                ->label('المادة')
                ->relationship('subject', 'name_ar', fn (Builder $q) => $q->where('is_active', true))
                ->required()
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name_ar')->label('اسم المادة (عربي)')->required(),
                    TextInput::make('name_en')->label('اسم المادة (English)'),
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
            ->columns([
                TextColumn::make('semester.year.name_ar')->label('السنة'),
                TextColumn::make('semester.name_ar')->label('الفصل'),
                TextColumn::make('subject.name_ar')->label('المادة')->searchable(),
                TextColumn::make('sort_order')->label('الترتيب')->sortable(),
                ToggleColumn::make('is_required')->label('إلزامية'),
                ToggleColumn::make('is_active')->label('نشط'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make()->label('إضافة مادة للمنهج'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['specialization_id'] = $this->getOwnerRecord()->getKey();

        return $data;
    }
}
