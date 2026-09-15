<?php

namespace App\Filament\Resources\Specializations\RelationManagers;

use App\Models\Semester;
use App\Models\Subject;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
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

    protected static ?string $modelLabel = 'مادة';

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
                ->options(fn () => Subject::query()
                    ->where('is_active', true)
                    ->orderBy('name_ar')
                    ->pluck('name_ar', 'id'))
                ->getSearchResultsUsing(fn (string $search) => Subject::query()
                    ->where('is_active', true)
                    ->where('name_ar', 'like', "%{$search}%")
                    ->orderBy('name_ar')
                    ->limit(50)
                    ->pluck('name_ar', 'id'))
                ->getOptionLabelUsing(fn ($value): ?string => Subject::query()->find($value)?->name_ar)
                ->required()
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name_ar')
                        ->label('اسم المادة (عربي)')
                        ->required()
                        ->live(debounce: 400)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                    TextInput::make('name_en')->label('اسم المادة (English)'),
                    TextInput::make('slug')
                        ->label('الرابط')
                        ->helperText('يُنشأ من الاسم العربي؛ يُضاف رقم تلقائياً إذا كان الرابط مستخدماً')
                        ->maxLength(255),
                    Toggle::make('is_active')->label('نشط')->default(true),
                ])
                ->createOptionUsing(function (array $data): int {
                    return Subject::query()->create($data)->getKey();
                }),
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
                CreateAction::make()
                    ->label('إضافة مادة للمنهج')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['specialization_id'] = $this->getOwnerRecord()->getKey();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
