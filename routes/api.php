<?php

use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\BunnyStreamWebhookController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\StudentAssignmentController;
use App\Http\Controllers\Api\StudentCertificateController;
use App\Http\Controllers\Api\StudentCourseController;
use App\Http\Controllers\Api\StudentDashboardController;
use App\Http\Controllers\Api\StudentEnrollmentController;
use App\Http\Controllers\Api\StudentGradeController;
use App\Http\Controllers\Api\StudentLessonAudioController;
use App\Http\Controllers\Api\StudentLessonController;
use App\Http\Controllers\Api\StudentProfileController;
use App\Http\Controllers\Api\StudentQuizController;
use App\Http\Controllers\Api\StudentScheduleController;
use App\Http\Controllers\Api\StudentSpecializationController;
use App\Http\Controllers\Api\StudentSubjectController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/academic/structure', [AcademicController::class, 'structure']);
    Route::get('/settings', [SettingController::class, 'show']);
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{course:slug}', [CourseController::class, 'show']);
    Route::get('/programs', [ProgramController::class, 'index']);
    Route::get('/programs/{program:slug}', [ProgramController::class, 'show']);
    Route::get('/teachers', [TeacherController::class, 'index']);
    Route::get('/teachers/{teacher:slug}', [TeacherController::class, 'show']);
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/announcements/{announcement:slug}', [AnnouncementController::class, 'show']);
    Route::get('/faqs', [FaqController::class, 'index']);
    Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact');
    Route::post('/enrollments', [EnrollmentController::class, 'store'])->middleware('throttle:enrollment');
    Route::post('/volunteer', [FeedbackController::class, 'volunteer'])->middleware('throttle:contact');

    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:auth');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:auth');

    Route::post('/webhooks/bunny-stream', BunnyStreamWebhookController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::middleware('student')->prefix('student')->group(function () {
            Route::get('/profile', [StudentProfileController::class, 'show']);
            Route::put('/profile', [StudentProfileController::class, 'update']);

            Route::middleware('active-student')->group(function () {
                Route::get('/dashboard', StudentDashboardController::class);
                Route::get('/courses', [StudentCourseController::class, 'index']);
                Route::post('/enrollments', [StudentEnrollmentController::class, 'store'])->middleware('throttle:enrollment');
                Route::get('/courses/{courseId}', [StudentCourseController::class, 'show']);
                Route::post('/lessons/{lessonId}/progress', [StudentLessonController::class, 'updateProgress']);
                Route::post('/lessons/{lessonId}/complete', [StudentLessonController::class, 'complete']);
                Route::get('/lessons/{lessonId}/audio', StudentLessonAudioController::class);
                Route::get('/lessons/{lessonId}/quiz', [StudentQuizController::class, 'show']);
                Route::post('/lessons/{lessonId}/quiz', [StudentQuizController::class, 'submit'])->middleware('throttle:quiz');
                Route::post('/error-reports', [FeedbackController::class, 'reportError']);
                Route::get('/schedule', StudentScheduleController::class);
                Route::get('/assignments', [StudentAssignmentController::class, 'index']);
                Route::post('/assignments/{assignmentId}/submit', [StudentAssignmentController::class, 'submit']);
                Route::get('/grades', StudentGradeController::class);
                Route::get('/certificates/{certificateId}', [StudentCertificateController::class, 'show']);
                Route::get('/specializations', [StudentSpecializationController::class, 'index']);
                Route::post('/specializations', [StudentSpecializationController::class, 'store'])->middleware('throttle:enrollment');
                Route::get('/curriculum', [StudentSpecializationController::class, 'curriculum']);
                Route::get('/subjects/{subject:slug}', [StudentSubjectController::class, 'show']);
            });
        });
    });
});
