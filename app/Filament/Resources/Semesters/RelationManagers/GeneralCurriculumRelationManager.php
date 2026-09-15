<?php

namespace App\Filament\Resources\Semesters\RelationManagers;

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
                CreateAction::make()
                    ->label('إضافة مادة')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['semester_id'] = $this->getOwnerRecord()->getKey();
                        $data['specialization_id'] = null;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
