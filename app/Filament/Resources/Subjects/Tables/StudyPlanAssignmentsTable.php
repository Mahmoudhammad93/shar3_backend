<?php

namespace App\Filament\Resources\Subjects\Tables;

use App\Filament\Resources\Subjects\Schemas\StudyPlanAssignmentForm;
use App\Models\CurriculumAssignment;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudyPlanAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('semester.year.level.name_ar')->label('المستوى'),
                TextColumn::make('semester.year.name_ar')->label('السنة'),
                TextColumn::make('semester.name_ar')->label('الفصل'),
                TextColumn::make('subject.name_ar')->label('المقرر الدراسي')->searchable(),
                TextColumn::make('subject.memorization_ar')->label('الحفظ')->limit(40)->placeholder('—'),
                TextColumn::make('subject.primary_text_ar')->label('اسم الكتاب')->limit(40)->placeholder('—'),
                TextColumn::make('subject.supplementary_text_ar')->label('تكميلي')->limit(40)->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('specialization.name_ar')->label('التخصص')->placeholder('—'),
                TextColumn::make('subject.course.title_ar')->label('الدورة المرتبطة')->placeholder('—'),
                IconColumn::make('is_required')->label('إلزامية')->boolean(),
                IconColumn::make('is_active')->label('نشط')->boolean(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->join('semesters', 'semesters.id', '=', 'curriculum_assignments.semester_id')
                ->join('academic_years', 'academic_years.id', '=', 'semesters.academic_year_id')
                ->join('academic_levels', 'academic_levels.id', '=', 'academic_years.academic_level_id')
                ->orderBy('academic_levels.number')
                ->orderBy('academic_years.year_number')
                ->orderBy('semesters.semester_number')
                ->orderBy('curriculum_assignments.sort_order')
                ->select('curriculum_assignments.*'))
            ->recordActions([
                EditAction::make()
                    ->label('تعديل')
                    ->modalHeading('تعديل مقرر الخطة الدراسية')
                    ->fillForm(fn (CurriculumAssignment $record): array => StudyPlanAssignmentForm::fillEditForm($record))
                    ->form(fn (CurriculumAssignment $record): array => StudyPlanAssignmentForm::editComponents($record))
                    ->action(function (CurriculumAssignment $record, array $data): void {
                        StudyPlanAssignmentForm::saveEdit($record, $data);

                        Notification::make()
                            ->title('تم حفظ المقرر')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->label('حذف')
                    ->modalHeading('حذف المقرر من الخطة')
                    ->modalDescription('سيتم حذف ربط المقرر بالخطة فقط، وليس تعريف المقرر نفسه.')
                    ->successNotificationTitle('تم حذف المقرر من الخطة'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('إضافة مقرر دراسي')
                    ->modalHeading('إضافة مقرر للخطة الدراسية')
                    ->form(StudyPlanAssignmentForm::createComponents())
                    ->action(function (array $data): void {
                        StudyPlanAssignmentForm::createAssignment($data);

                        Notification::make()
                            ->title('تمت إضافة المقرر للخطة')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
