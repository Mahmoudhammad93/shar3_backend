<?php

namespace App\Filament\Resources\Students\Tables;

use App\Actions\ApproveStudentRegistrationAction;
use App\Actions\RejectStudentRegistrationAction;
use App\Filament\Resources\Students\Schemas\StudentForm;
use App\Models\Student;
use App\Support\Countries;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),
                ImageColumn::make('photo')
                    ->label('الصورة')
                    ->circular()
                    ->size(40),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('الجوال')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => Student::statusOptions()[$state] ?? 'غير معروف')
                    ->color(fn (int $state): string => match ($state) {
                        Student::STATUS_PENDING => 'warning',
                        Student::STATUS_ACTIVE => 'success',
                        Student::STATUS_GRADUATED => 'info',
                        Student::STATUS_SUSPENDED => 'danger',
                        Student::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('academicLevel.name_ar')
                    ->label('المستوى الأكاديمي')
                    ->placeholder('—'),
                TextColumn::make('academicYear.name_ar')
                    ->label('السنة الدراسية')
                    ->placeholder('—'),
                TextColumn::make('currentSemester.name_ar')
                    ->label('الفصل الحالي')
                    ->placeholder('—'),
                TextColumn::make('gender')
                    ->label('الجنس')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? (Student::genderOptions()[$state] ?? $state) : null)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birth_date')
                    ->label('تاريخ الميلاد')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country')
                    ->label('الدولة')
                    ->formatStateUsing(fn (?string $state): ?string => Countries::displayName($state))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('approved_at')
                    ->label('تاريخ الاعتماد')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(Student::statusOptions()),
                SelectFilter::make('gender')
                    ->label('الجنس')
                    ->options(Student::genderOptions()),
                SelectFilter::make('country')
                    ->label('الدولة')
                    ->options(Countries::options())
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('اعتماد')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('اعتماد طلب التسجيل')
                    ->modalDescription('سيتم تفعيل الطالب وتسكينه تلقائياً في المستوى التمهيدي — السنة الأولى — الفصل الحالي.')
                    ->visible(fn (Student $record): bool => $record->status === Student::STATUS_PENDING)
                    ->action(function (Student $record, ApproveStudentRegistrationAction $approve): void {
                        $approve->execute($record, auth()->user());

                        Notification::make()
                            ->title('تم اعتماد الطالب')
                            ->body('تم تفعيل الحساب وتسكينه في السنة الأولى التمهيدية.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Student $record): bool => $record->status === Student::STATUS_PENDING)
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Student $record, array $data, RejectStudentRegistrationAction $reject): void {
                        $reject->execute($record, auth()->user(), $data['rejection_reason']);

                        Notification::make()
                            ->title('تم رفض طلب التسجيل')
                            ->warning()
                            ->send();
                    }),
                EditAction::make()
                    ->label('تعديل')
                    ->modalHeading('تعديل بيانات الطالب')
                    ->modalWidth(Width::SevenExtraLarge)
                    ->fillForm(fn (Student $record): array => StudentForm::prepareForFill($record->attributesToArray()))
                    ->mutateFormDataUsing(function (array $data): array {
                        if (empty($data['name'])) {
                            $data['name'] = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? ''));
                        }

                        return $data;
                    }),
                DeleteAction::make()
                    ->label('حذف')
                    ->modalHeading('حذف الطالب')
                    ->modalDescription('سيتم حذف سجل الطالب نهائياً.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
