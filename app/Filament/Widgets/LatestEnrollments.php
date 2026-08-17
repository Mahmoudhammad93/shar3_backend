<?php

namespace App\Filament\Widgets;

use App\Models\Enrollment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestEnrollments extends TableWidget
{
    protected static ?string $heading = 'أحدث طلبات التسجيل';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Enrollment::query()
                    ->with(['student', 'course'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('student.name')->label('الطالب'),
                TextColumn::make('course.title_ar')->label('الدورة'),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                        'completed' => 'مكتمل',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d H:i'),
            ])
            ->paginated(false);
    }
}
