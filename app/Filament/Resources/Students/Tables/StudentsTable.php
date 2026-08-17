<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\Student;
use App\Support\Countries;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                TextColumn::make('gender')
                    ->label('الجنس')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? (Student::genderOptions()[$state] ?? $state) : null),
                TextColumn::make('birth_date')
                    ->label('تاريخ الميلاد')
                    ->date()
                    ->sortable(),
                TextColumn::make('country')
                    ->label('الدولة')
                    ->formatStateUsing(fn (?string $state): ?string => Countries::displayName($state))
                    ->searchable(),
                TextColumn::make('city')
                    ->label('المدينة')
                    ->searchable(),
                TextColumn::make('national_id')
                    ->label('رقم الهوية')
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
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('تاريخ التحديث')
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
