<?php

namespace App\Filament\Resources\Subjects\Schemas;

use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CurriculumAssignment;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Subject;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudyPlanAssignmentForm
{
    /** @return array<int, \Filament\Forms\Components\Component> */
    public static function editComponents(CurriculumAssignment $record): array
    {
        $record->loadMissing(['semester.year.level', 'specialization', 'subject']);

        return [
            Section::make('الموضع في الخطة')
                ->schema(self::placementComponents())
                ->columns(2)
                ->columnSpanFull(),
            ...self::subjectComponents(ignoreSubjectId: $record->subject_id),
            ...self::assignmentComponents(),
        ];
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    public static function createComponents(): array
    {
        return [
            Section::make('الموضع في الخطة')
                ->schema(self::placementComponents())
                ->columns(2)
                ->columnSpanFull(),
            ...self::subjectComponents(includeSlug: true),
            ...self::assignmentComponents(),
        ];
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    private static function placementComponents(): array
    {
        return [
            Select::make('academic_level_id')
                ->label('المستوى')
                ->options(fn () => AcademicLevel::query()
                    ->where('is_active', true)
                    ->orderBy('number')
                    ->pluck('name_ar', 'id'))
                ->required()
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $set('academic_year_id', null);
                    $set('semester_id', null);
                }),
            Select::make('academic_year_id')
                ->label('السنة')
                ->options(fn (Get $get) => AcademicYear::query()
                    ->where('is_active', true)
                    ->when(
                        $get('academic_level_id'),
                        fn (Builder $query, $levelId) => $query->where('academic_level_id', $levelId),
                    )
                    ->orderBy('year_number')
                    ->pluck('name_ar', 'id'))
                ->required()
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('semester_id', null)),
            Select::make('semester_id')
                ->label('الفصل')
                ->options(fn (Get $get) => Semester::query()
                    ->where('is_active', true)
                    ->when(
                        $get('academic_year_id'),
                        fn (Builder $query, $yearId) => $query->where('academic_year_id', $yearId),
                    )
                    ->orderBy('semester_number')
                    ->pluck('name_ar', 'id'))
                ->required()
                ->searchable()
                ->preload(),
            Select::make('specialization_id')
                ->label('التخصص')
                ->options(fn () => Specialization::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->pluck('name_ar', 'id'))
                ->placeholder('— عام —')
                ->searchable()
                ->preload(),
        ];
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    private static function subjectComponents(bool $includeSlug = false, ?int $ignoreSubjectId = null): array
    {
        $components = [
            TextInput::make('name_ar')
                ->label('اسم المقرر بالعربية')
                ->required()
                ->live(debounce: 400)
                ->afterStateUpdated(function (Set $set, ?string $state) use ($includeSlug): void {
                    if ($includeSlug) {
                        $set('slug', Str::slug($state ?? ''));
                    }
                }),
            TextInput::make('name_en')
                ->label('اسم المقرر بالإنجليزية'),
        ];

        if ($includeSlug) {
            $components[] = TextInput::make('slug')
                ->label('الرابط المختصر')
                ->required()
                ->maxLength(255)
                ->alphaDash()
                ->helperText('يُنشأ تلقائياً من اسم المقرر');
        }

        $components[] = Select::make('course_id')
            ->label('ربط بدورة موجودة')
            ->options(fn () => self::availableCourseOptions($ignoreSubjectId))
            ->searchable()
            ->preload()
            ->helperText('كل دورة يمكن ربطها بمقرر واحد فقط في الخطة الدراسية')
            ->rules([
                fn (): \Closure => function (string $attribute, $value, \Closure $fail) use ($ignoreSubjectId): void {
                    if ($value !== null && $value !== '' && self::courseAlreadyLinked((int) $value, $ignoreSubjectId)) {
                        $fail('هذه الدورة مرتبطة بمقرر آخر في الخطة الدراسية.');
                    }
                },
            ]);

        $components[] = Section::make('محتوى الخطة الدراسية')->schema([
            Textarea::make('memorization_ar')
                ->label('الحفظ')
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('primary_text_ar')
                ->label('اسم الكتاب / المتون الأساسية')
                ->rows(4)
                ->helperText('يظهر في عمود «اسم الكتاب» في الموقع')
                ->columnSpanFull(),
            Textarea::make('supplementary_text_ar')
                ->label('الكتب التكميلية')
                ->rows(4)
                ->columnSpanFull(),
        ])->columnSpanFull();

        return $components;
    }

    /** @return array<int, \Filament\Forms\Components\Component> */
    private static function assignmentComponents(): array
    {
        return [
            TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('is_required')
                ->label('مقرر إلزامي')
                ->default(true),
            Toggle::make('is_active')
                ->label('نشط في الخطة')
                ->default(true),
        ];
    }

    /** @param  array<string, mixed>  $data */
    public static function fillEditForm(CurriculumAssignment $record): array
    {
        $record->loadMissing(['subject', 'semester.year']);

        return [
            'academic_level_id' => $record->semester?->year?->academic_level_id,
            'academic_year_id' => $record->semester?->academic_year_id,
            'semester_id' => $record->semester_id,
            'specialization_id' => $record->specialization_id,
            'name_ar' => $record->subject?->name_ar,
            'name_en' => $record->subject?->name_en,
            'course_id' => $record->subject?->course_id,
            'memorization_ar' => $record->subject?->memorization_ar,
            'primary_text_ar' => $record->subject?->primary_text_ar,
            'supplementary_text_ar' => $record->subject?->supplementary_text_ar,
            'sort_order' => $record->sort_order,
            'is_required' => $record->is_required,
            'is_active' => $record->is_active,
        ];
    }

    /** @return array<int, string> */
    public static function availableCourseOptions(?int $ignoreSubjectId = null): array
    {
        $linkedCourseIds = Subject::query()
            ->when($ignoreSubjectId, fn (Builder $query) => $query->where('id', '!=', $ignoreSubjectId))
            ->whereNotNull('course_id')
            ->pluck('course_id');

        return Course::query()
            ->where('is_published', true)
            ->when(
                $linkedCourseIds->isNotEmpty(),
                fn (Builder $query) => $query->whereNotIn('id', $linkedCourseIds),
            )
            ->orderBy('title_ar')
            ->pluck('title_ar', 'id')
            ->all();
    }

    public static function courseAlreadyLinked(int $courseId, ?int $ignoreSubjectId = null): bool
    {
        return Subject::query()
            ->where('course_id', $courseId)
            ->when($ignoreSubjectId, fn (Builder $query) => $query->where('id', '!=', $ignoreSubjectId))
            ->exists();
    }

    public static function assertCourseLinkAvailable(?int $courseId, ?int $ignoreSubjectId = null): void
    {
        if ($courseId === null || ! self::courseAlreadyLinked($courseId, $ignoreSubjectId)) {
            return;
        }

        throw ValidationException::withMessages([
            'course_id' => 'هذه الدورة مرتبطة بمقرر آخر في الخطة الدراسية.',
        ]);
    }

    /** @param  array<string, mixed>  $data */
    public static function saveEdit(CurriculumAssignment $record, array $data): void
    {
        $record->loadMissing('subject');

        $courseId = filled($data['course_id'] ?? null) ? (int) $data['course_id'] : null;

        self::assertCourseLinkAvailable($courseId, $record->subject?->id);

        $record->subject?->update([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'course_id' => $courseId,
            'memorization_ar' => $data['memorization_ar'] ?? null,
            'primary_text_ar' => $data['primary_text_ar'] ?? null,
            'supplementary_text_ar' => $data['supplementary_text_ar'] ?? null,
        ]);

        $record->update([
            'semester_id' => (int) $data['semester_id'],
            'specialization_id' => filled($data['specialization_id'] ?? null)
                ? (int) $data['specialization_id']
                : null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_required' => (bool) ($data['is_required'] ?? true),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }

    /** @param  array<string, mixed>  $data */
    public static function createAssignment(array $data): CurriculumAssignment
    {
        $courseId = filled($data['course_id'] ?? null) ? (int) $data['course_id'] : null;

        self::assertCourseLinkAvailable($courseId);

        $subject = Subject::query()->create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
            'slug' => $data['slug'] ?? Str::slug($data['name_ar']),
            'course_id' => $courseId,
            'memorization_ar' => $data['memorization_ar'] ?? null,
            'primary_text_ar' => $data['primary_text_ar'] ?? null,
            'supplementary_text_ar' => $data['supplementary_text_ar'] ?? null,
            'is_active' => true,
        ]);

        return CurriculumAssignment::query()->create([
            'semester_id' => $data['semester_id'],
            'subject_id' => $subject->id,
            'specialization_id' => $data['specialization_id'] ?? null,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_required' => (bool) ($data['is_required'] ?? true),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }
}
