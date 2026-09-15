<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Students\StudentResource;
use App\Models\Student;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestStudentRegistrations extends TableWidget
{
    protected static ?string $heading = 'أحدث طلبات تسجيل الحسابات';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()->latest()->limit(8)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('الطالب')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('البريد الإلكتروني'),
                TextColumn::make('phone')
                    ->label('الجوال')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (int $state): string => Student::statusOptions()[$state] ?? 'غير معروف')
                    ->color(fn (int $state): string => match ($state) {
                        Student::STATUS_PENDING => 'warning',
                        Student::STATUS_ACTIVE => 'success',
                        Student::STATUS_REJECTED => 'danger',
                        Student::STATUS_SUSPENDED => 'danger',
                        Student::STATUS_GRADUATED => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y-m-d H:i'),
            ])
            ->recordUrl(fn (Student $record): string => StudentResource::getUrl('index'))
            ->paginated(false)
            ->emptyStateHeading('لا توجد طلبات تسجيل')
            ->emptyStateDescription('ستظهر هنا طلبات إنشاء حسابات الطلاب الجديدة.');
    }
}
