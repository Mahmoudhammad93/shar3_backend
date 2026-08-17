<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id', 'name', 'first_name', 'last_name', 'email', 'phone', 'whatsapp',
    'gender', 'birth_date', 'country', 'nationality', 'city',
    'education_level', 'heard_about', 'works_full_time', 'participates_other_programs',
    'daily_hours', 'terms_accepted_at',
    'national_id', 'status', 'notes', 'photo',
    'academic_level_id', 'academic_year_id',
])]
class Student extends Model
{
    public const STATUS_PENDING = 0;

    public const STATUS_ACTIVE = 1;

    public const STATUS_GRADUATED = 2;

    public const STATUS_SUSPENDED = 3;

    /** @return array<int, string> */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_ACTIVE => 'نشط',
            self::STATUS_GRADUATED => 'متخرج',
            self::STATUS_SUSPENDED => 'موقوف',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusOptions()[$this->status] ?? 'غير معروف';
    }

    public static function genderOptions(): array
    {
        return [
            'male' => 'ذكر',
            'female' => 'أنثى',
        ];
    }

    public function genderLabel(): ?string
    {
        if ($this->gender === null) {
            return null;
        }

        return self::genderOptions()[$this->gender] ?? $this->gender;
    }

    /** @return array<string, string> */
    public static function educationLevelOptions(): array
    {
        return [
            'primary' => 'ابتدائي',
            'preparatory' => 'إعدادي',
            'secondary' => 'ثانوي',
            'diploma' => 'معهد / دبلوم',
            'bachelor' => 'جامعي',
            'master' => 'ماجستير',
            'phd' => 'دكتوراه',
            'other' => 'أخرى',
        ];
    }

    public function educationLevelLabel(): ?string
    {
        if ($this->education_level === null) {
            return null;
        }

        return self::educationLevelOptions()[$this->education_level] ?? $this->education_level;
    }

    /** @return array<string, string> */
    public static function heardAboutOptions(): array
    {
        return [
            'friend' => 'صديق',
            'social' => 'وسائل التواصل',
            'search' => 'محرك بحث',
            'teacher' => 'أحد المشايخ',
            'other' => 'أخرى',
        ];
    }

    public function heardAboutLabel(): ?string
    {
        if ($this->heard_about === null) {
            return null;
        }

        return self::heardAboutOptions()[$this->heard_about] ?? $this->heard_about;
    }

    /** @return array<string, string> */
    public static function dailyHoursOptions(): array
    {
        return [
            '1-2' => '1-2 ساعة',
            '2-4' => '2-4 ساعات',
            '4-6' => '4-6 ساعات',
            '6+' => 'أكثر من 6 ساعات',
        ];
    }

    public function dailyHoursLabel(): ?string
    {
        if ($this->daily_hours === null) {
            return null;
        }

        return self::dailyHoursOptions()[$this->daily_hours] ?? $this->daily_hours;
    }

    public function booleanLabel(?bool $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value ? 'نعم' : 'لا';
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'status' => 'integer',
            'works_full_time' => 'boolean',
            'participates_other_programs' => 'boolean',
            'terms_accepted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'student_specializations')
            ->using(StudentSpecializationPivot::class)
            ->withPivot(['status', 'selected_at'])
            ->withTimestamps();
    }

    public function specializationChoices(): HasMany
    {
        return $this->hasMany(StudentSpecialization::class);
    }

    /** @deprecated Use specializations() */
    public function specializationChoice(): HasOne
    {
        return $this->hasOne(StudentSpecialization::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot(['status', 'enrolled_at', 'completed_at', 'notes'])
            ->withTimestamps();
    }
}
