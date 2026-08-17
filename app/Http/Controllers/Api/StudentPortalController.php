<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Countries;
use App\Support\MediaUrl;
use App\Models\Assignment;
use App\Models\Certificate;
use App\Models\SiteSetting;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\LessonQuestion;
use App\Models\Schedule;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $enrollments = Enrollment::query()
            ->with(['course.lessons'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['approved', 'completed'])
            ->get();

        $pendingEnrollments = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', 'pending')
            ->count();

        $totalLessons = 0;
        $completedLessons = 0;

        foreach ($enrollments as $enrollment) {
            $courseLessons = $enrollment->course?->lessons ?? collect();
            $totalLessons += $courseLessons->count();
            $completedLessons += LessonProgress::query()
                ->where('student_id', $student->id)
                ->whereIn('lesson_id', $courseLessons->pluck('id'))
                ->where('is_completed', true)
                ->count();
        }

        $progressPercent = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;

        $pendingAssignments = Assignment::query()
            ->whereIn('course_id', $enrollments->pluck('course_id'))
            ->where('is_published', true)
            ->count();

        return response()->json([
            'stats' => [
                'active_courses' => $enrollments->count(),
                'pending_enrollments' => $pendingEnrollments,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'progress_percent' => $progressPercent,
                'pending_assignments' => $pendingAssignments,
                'certificates' => Certificate::query()->where('student_id', $student->id)->count(),
            ],
            'recent_courses' => $enrollments->take(3)->map(fn ($e) => [
                'id' => $e->course?->id,
                'title_ar' => $e->course?->title_ar,
                'slug' => $e->course?->slug,
                'progress' => $this->courseProgress($student, $e->course_id),
            ]),
        ]);
    }

    public function courses(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $enrollments = Enrollment::query()
            ->with(['course.teacher', 'course.lessons', 'course.category'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->orderByRaw("FIELD(status, 'approved', 'completed', 'pending', 'rejected')")
            ->get();

        return response()->json([
            'data' => $enrollments->map(function ($e) use ($student) {
                $lessonsCount = $e->course?->lessons->count() ?? 0;
                $progress = in_array($e->status, ['approved', 'completed'], true)
                    ? $this->courseProgress($student, $e->course_id)
                    : 0;
                $completedLessons = $lessonsCount > 0
                    ? (int) round($progress * $lessonsCount / 100)
                    : 0;

                return [
                    'enrollment_id' => $e->id,
                    'status' => $e->status,
                    'course' => [
                        'id' => $e->course?->id,
                        'title_ar' => $e->course?->title_ar,
                        'slug' => $e->course?->slug,
                        'image' => MediaUrl::resolve($e->course?->image),
                        'description_ar' => $e->course?->description_ar,
                        'category' => $e->course?->category?->name_ar ?? $e->course?->category?->name_en,
                        'teacher' => $e->course?->teacher?->name_ar,
                        'lessons_count' => $lessonsCount,
                        'completed_lessons' => $completedLessons,
                    ],
                    'progress' => $progress,
                ];
            }),
        ]);
    }

    public function enroll(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $course = \App\Models\Course::query()
            ->where('is_published', true)
            ->findOrFail($validated['course_id']);

        $existing = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'rejected') {
                $existing->update([
                    'status' => 'approved',
                    'enrolled_at' => now(),
                    'notes' => $validated['notes'] ?? $existing->notes,
                ]);

                return response()->json([
                    'message' => 'تم إعادة تسجيلك في الدورة بنجاح.',
                    'enrollment' => ['status' => 'approved'],
                ]);
            }

            $message = match ($existing->status) {
                'pending' => 'طلب التسجيل قيد المراجعة.',
                'approved', 'completed' => 'أنت مسجّل بالفعل في هذه الدورة.',
                default => 'تم إرسال طلب التسجيل مسبقاً.',
            };

            return response()->json([
                'message' => $message,
                'enrollment' => ['status' => $existing->status],
            ], in_array($existing->status, ['approved', 'completed'], true) ? 200 : 202);
        }

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'approved',
            'enrolled_at' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'message' => 'تم تسجيلك في الدورة بنجاح. يمكنك البدء من لوحة التحكم.',
            'enrollment' => ['status' => 'approved'],
        ], 201);
    }

    public function courseDetail(Request $request, int $courseId): JsonResponse
    {
        $student = $this->student($request);

        $enrollment = Enrollment::query()
            ->with([
                'course.teacher',
                'course.lessons' => fn ($q) => $q->where('is_published', true)->orderBy('sort_order')->withCount('questions'),
            ])
            ->where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->whereIn('status', ['approved', 'completed'])
            ->firstOrFail();

        $lessons = $enrollment->course->lessons;

        $progressMap = LessonProgress::query()
            ->where('student_id', $student->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $orderedLessons = $lessons->values();

        return response()->json([
            'course' => [
                'id' => $enrollment->course->id,
                'title_ar' => $enrollment->course->title_ar,
                'slug' => $enrollment->course->slug,
                'image' => MediaUrl::resolve($enrollment->course->image),
                'description_ar' => $enrollment->course->description_ar,
                'teacher' => $enrollment->course->teacher?->name_ar,
            ],
            'progress' => $this->courseProgress($student, $courseId),
            'lessons' => $orderedLessons->map(function ($lesson, $index) use ($orderedLessons, $progressMap) {
                $previousLesson = $index > 0 ? $orderedLessons[$index - 1] : null;
                $previousCompleted = $previousLesson
                    ? ($progressMap->get($previousLesson->id)?->is_completed ?? false)
                    : true;

                return [
                    'id' => $lesson->id,
                    'title_ar' => $lesson->title_ar,
                    'content_ar' => $lesson->content_ar,
                    'video_url' => $lesson->video_url,
                    'duration_minutes' => $lesson->duration_minutes,
                    'sort_order' => $lesson->sort_order,
                    'is_completed' => $progressMap->get($lesson->id)?->is_completed ?? false,
                    'progress_percent' => $progressMap->get($lesson->id)?->progress_percent ?? 0,
                    'quiz_passed' => $progressMap->get($lesson->id)?->quiz_passed ?? false,
                    'has_quiz' => ($lesson->questions_count ?? 0) > 0,
                    'is_locked' => ! $previousCompleted,
                ];
            }),
        ]);
    }

    public function updateLessonProgress(Request $request, int $lessonId): JsonResponse
    {
        $student = $this->student($request);
        $lesson = Lesson::query()->findOrFail($lessonId);

        $validated = $request->validate([
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $this->ensureLessonAccessible($student, $lesson);

        $existing = LessonProgress::query()
            ->where('student_id', $student->id)
            ->where('lesson_id', $lessonId)
            ->first();

        $progressPercent = max(
            $existing?->progress_percent ?? 0,
            $validated['progress_percent']
        );

        LessonProgress::query()->updateOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lessonId],
            ['progress_percent' => $progressPercent]
        );

        return response()->json([
            'message' => 'تم حفظ التقدم',
            'progress_percent' => $progressPercent,
        ]);
    }

    public function completeLesson(Request $request, int $lessonId): JsonResponse
    {
        $student = $this->student($request);
        $lesson = Lesson::query()->findOrFail($lessonId);

        $this->ensureLessonAccessible($student, $lesson);

        if ($lesson->video_url) {
            $progress = LessonProgress::query()
                ->where('student_id', $student->id)
                ->where('lesson_id', $lessonId)
                ->first();

            abort_unless(
                ($progress?->progress_percent ?? 0) >= 95,
                422,
                'يجب مشاهدة الفيديو كاملاً قبل إكمال الدرس'
            );
        }

        $lesson->loadCount('questions');

        if ($lesson->questions_count > 0) {
            $progress = LessonProgress::query()
                ->where('student_id', $student->id)
                ->where('lesson_id', $lessonId)
                ->first();

            abort_unless(
                $progress?->quiz_passed === true,
                422,
                'يجب اجتياز أسئلة الدرس قبل الإكمال'
            );
        }

        LessonProgress::query()->updateOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lessonId],
            ['progress_percent' => 100, 'is_completed' => true, 'completed_at' => now()]
        );

        return response()->json(['message' => 'تم إكمال الدرس']);
    }

    public function lessonQuiz(Request $request, int $lessonId): JsonResponse
    {
        $student = $this->student($request);
        $lesson = Lesson::query()->with(['questions.options'])->findOrFail($lessonId);

        $this->ensureLessonAccessible($student, $lesson);

        abort_unless($lesson->questions->isNotEmpty(), 404, 'لا توجد أسئلة لهذا الدرس');

        return response()->json([
            'lesson_id' => $lesson->id,
            'questions' => $lesson->questions->map(fn ($question) => [
                'id' => $question->id,
                'type' => $question->type ?? LessonQuestion::TYPE_CHOICE,
                'question_ar' => $question->question_ar,
                'options' => $question->isChoice()
                    ? $question->options->map(fn ($option) => [
                        'id' => $option->id,
                        'option_ar' => $option->option_ar,
                    ])->values()
                    : [],
            ]),
        ]);
    }

    public function submitLessonQuiz(Request $request, int $lessonId): JsonResponse
    {
        $student = $this->student($request);
        $lesson = Lesson::query()->with(['questions.options'])->findOrFail($lessonId);

        $this->ensureLessonAccessible($student, $lesson);

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required'],
        ]);

        $questions = $lesson->questions;

        abort_unless($questions->isNotEmpty(), 404);

        foreach ($questions as $question) {
            $answer = $validated['answers'][$question->id] ?? null;

            if (! $this->isQuizAnswerCorrect($question, $answer)) {
                return response()->json([
                    'message' => 'إجابة غير صحيحة. راجع الدرس وحاول مرة أخرى.',
                    'passed' => false,
                ], 422);
            }
        }

        LessonProgress::query()->updateOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lessonId],
            ['quiz_passed' => true]
        );

        return response()->json([
            'message' => 'أحسنت! اجتزت أسئلة الدرس.',
            'passed' => true,
        ]);
    }

    private function isQuizAnswerCorrect(LessonQuestion $question, mixed $answer): bool
    {
        if ($answer === null || $answer === '') {
            return false;
        }

        return match ($question->type ?? LessonQuestion::TYPE_CHOICE) {
            LessonQuestion::TYPE_TRUE_FALSE => $this->normalizeTrueFalseAnswer($answer) === $question->correct_answer,
            LessonQuestion::TYPE_TEXT => $this->normalizeQuizText((string) $answer) === $this->normalizeQuizText((string) $question->correct_answer),
            default => $this->isChoiceAnswerCorrect($question, $answer),
        };
    }

    private function isChoiceAnswerCorrect(LessonQuestion $question, mixed $answer): bool
    {
        $correctOption = $question->options->firstWhere('is_correct', true);

        return $correctOption && (int) $answer === (int) $correctOption->id;
    }

    private function normalizeTrueFalseAnswer(mixed $answer): ?string
    {
        if ($answer === true || $answer === 'true' || $answer === '1' || $answer === 1) {
            return 'true';
        }

        if ($answer === false || $answer === 'false' || $answer === '0' || $answer === 0) {
            return 'false';
        }

        return null;
    }

    private function normalizeQuizText(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return $text;
    }

    private function ensureLessonAccessible(Student $student, Lesson $lesson): void
    {
        $enrolled = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('course_id', $lesson->course_id)
            ->whereIn('status', ['approved', 'completed'])
            ->exists();

        abort_unless($enrolled, 403);

        $previousIncomplete = Lesson::query()
            ->where('course_id', $lesson->course_id)
            ->where('is_published', true)
            ->where('sort_order', '<', $lesson->sort_order)
            ->whereDoesntHave('progress', fn ($q) => $q
                ->where('student_id', $student->id)
                ->where('is_completed', true))
            ->exists();

        abort_if($previousIncomplete, 403, 'يجب إكمال الدرس السابق أولاً');
    }

    public function schedule(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $courseIds = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id');

        $schedules = Schedule::query()
            ->with('course:id,title_ar')
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->where('starts_at', '>=', now()->subDays(7))
            ->orderBy('starts_at')
            ->get();

        return response()->json([
            'data' => $schedules->map(fn ($s) => [
                'id' => $s->id,
                'title_ar' => $s->title_ar,
                'description_ar' => $s->description_ar,
                'type' => $s->type,
                'starts_at' => $s->starts_at->format('Y-m-d H:i'),
                'ends_at' => $s->ends_at?->format('Y-m-d H:i'),
                'meeting_url' => $s->meeting_url,
                'course' => $s->course?->title_ar,
            ]),
        ]);
    }

    public function assignments(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $courseIds = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->pluck('course_id');

        $assignments = Assignment::query()
            ->with(['course:id,title_ar', 'submissions' => fn ($q) => $q->where('student_id', $student->id)])
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->orderBy('due_at')
            ->get();

        return response()->json([
            'data' => $assignments->map(fn ($a) => [
                'id' => $a->id,
                'title_ar' => $a->title_ar,
                'description_ar' => $a->description_ar,
                'due_at' => $a->due_at?->format('Y-m-d H:i'),
                'max_score' => $a->max_score,
                'course' => $a->course?->title_ar,
                'submission' => $a->submissions->first() ? [
                    'status' => $a->submissions->first()->status,
                    'score' => $a->submissions->first()->score,
                    'submitted_at' => $a->submissions->first()->submitted_at?->format('Y-m-d H:i'),
                ] : null,
            ]),
        ]);
    }

    public function submitAssignment(Request $request, int $assignmentId): JsonResponse
    {
        $student = $this->student($request);
        $assignment = Assignment::query()->findOrFail($assignmentId);

        $enrolled = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('course_id', $assignment->course_id)
            ->where('status', 'approved')
            ->exists();

        abort_unless($enrolled, 403);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:10000'],
        ]);

        $student->assignmentSubmissions()->updateOrCreate(
            ['assignment_id' => $assignmentId],
            ['content' => $validated['content'], 'status' => 'submitted', 'submitted_at' => now()]
        );

        return response()->json(['message' => 'تم تسليم الواجب بنجاح']);
    }

    public function grades(Request $request): JsonResponse
    {
        $student = $this->student($request);

        $submissions = $student->assignmentSubmissions()
            ->with(['assignment.course:id,title_ar'])
            ->where('status', 'graded')
            ->get();

        $certificates = Certificate::query()
            ->with('course:id,title_ar')
            ->where('student_id', $student->id)
            ->get();

        return response()->json([
            'grades' => $submissions->map(fn ($s) => [
                'assignment' => $s->assignment?->title_ar,
                'course' => $s->assignment?->course?->title_ar,
                'score' => $s->score,
                'max_score' => $s->assignment?->max_score,
                'feedback' => $s->feedback,
            ]),
            'certificates' => $certificates->map(fn ($c) => [
                'id' => $c->id,
                'course' => $c->course?->title_ar,
                'certificate_number' => $c->certificate_number,
                'issued_at' => $c->issued_at->format('Y-m-d'),
            ]),
        ]);
    }

    public function showCertificate(Request $request, int $certificateId): JsonResponse
    {
        $student = $this->student($request);

        $certificate = Certificate::query()
            ->with(['course.teacher:id,name_ar,title_ar', 'student:id,name'])
            ->where('student_id', $student->id)
            ->findOrFail($certificateId);

        $settings = SiteSetting::current();

        return response()->json([
            'certificate' => [
                'id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'issued_at' => $certificate->issued_at->format('Y-m-d'),
                'issued_at_label' => $certificate->issued_at->locale('ar')->translatedFormat('j F Y'),
                'student_name' => $certificate->student?->name ?? $student->name,
                'course_title' => $certificate->course?->title_ar,
                'teacher_name' => $certificate->course?->teacher?->name_ar,
                'teacher_title' => $certificate->course?->teacher?->title_ar,
                'institute_name' => $settings->dashboard_institute_name_ar ?: $settings->site_name_ar,
                'institute_tagline' => $settings->tagline_ar,
                'academic_year' => $settings->academic_year_ar,
                'file_url' => MediaUrl::resolve($certificate->file_path),
            ],
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $student = $this->student($request);
        $user = $request->user();

        return response()->json([
            'user' => ['name' => $user->name, 'email' => $user->email],
            'student' => $this->studentProfilePayload($student),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $student = $this->student($request);
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'country' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        if (isset($validated['name'])) {
            $user->update(['name' => $validated['name']]);
            $student->update(['name' => $validated['name']]);
        }

        $student->update(collect($validated)->except('name')->toArray());

        return response()->json([
            'message' => 'تم تحديث الملف الشخصي',
            'student' => $this->studentProfilePayload($student->fresh()),
        ]);
    }

    /** @return array<string, mixed> */
    private function studentProfilePayload(Student $student): array
    {
        return [
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'phone' => $student->phone,
            'whatsapp' => $student->whatsapp,
            'gender' => $student->gender,
            'gender_label' => $student->genderLabel(),
            'birth_date' => $student->birth_date?->format('Y-m-d'),
            'nationality' => $student->nationality,
            'country' => $student->country,
            'country_label' => Countries::displayName($student->country),
            'city' => $student->city,
            'national_id' => $student->national_id,
            'education_level' => $student->education_level,
            'education_level_label' => $student->educationLevelLabel(),
            'heard_about' => $student->heard_about,
            'heard_about_label' => $student->heardAboutLabel(),
            'works_full_time' => $student->works_full_time,
            'works_full_time_label' => $student->booleanLabel($student->works_full_time),
            'participates_other_programs' => $student->participates_other_programs,
            'participates_other_programs_label' => $student->booleanLabel($student->participates_other_programs),
            'daily_hours' => $student->daily_hours,
            'daily_hours_label' => $student->dailyHoursLabel(),
            'terms_accepted_at' => $student->terms_accepted_at?->format('Y-m-d H:i'),
            'status' => $student->status,
            'status_label' => $student->statusLabel(),
            'photo' => MediaUrl::resolve($student->photo),
        ];
    }

    private function student(Request $request): Student
    {
        $student = $request->user()->student;

        abort_unless($student, 403, 'Student profile not found');

        return $student;
    }

    private function courseProgress(Student $student, ?int $courseId): int
    {
        if (! $courseId) {
            return 0;
        }

        $lessons = Lesson::query()->where('course_id', $courseId)->pluck('id');
        if ($lessons->isEmpty()) {
            return 0;
        }

        $completed = LessonProgress::query()
            ->where('student_id', $student->id)
            ->whereIn('lesson_id', $lessons)
            ->where('is_completed', true)
            ->count();

        return (int) round(($completed / $lessons->count()) * 100);
    }
}
