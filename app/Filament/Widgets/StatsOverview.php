<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pendingStudents = Student::query()->where('status', Student::STATUS_PENDING)->count();
        $pendingCourseEnrollments = Enrollment::query()->where('status', 'pending')->count();
        $newMessages = ContactMessage::query()->where('status', 'new')->count();

        return [
            Stat::make('الطلاب', Student::query()->count())
                ->description('إجمالي الطلاب المسجلين')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),
            Stat::make('الدورات', Course::query()->count())
                ->description(Course::query()->where('is_published', true)->count().' منشورة')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),
            Stat::make('المعلمون', Teacher::query()->where('is_active', true)->count())
                ->description('معلمون نشطون')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
            Stat::make('طلبات تسجيل حسابات', $pendingStudents)
                ->description('طلاب بانتظار الاعتماد')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($pendingStudents > 0 ? 'warning' : 'gray'),
            Stat::make('طلبات دورات عامة', $pendingCourseEnrollments)
                ->description('تسجيل في دورات إضافية')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color($pendingCourseEnrollments > 0 ? 'warning' : 'gray'),
            Stat::make('رسائل جديدة', $newMessages)
                ->description('رسائل التواصل')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($newMessages > 0 ? 'danger' : 'gray'),
        ];
    }
}
