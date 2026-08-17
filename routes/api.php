<?php

use App\Http\Controllers\Api\AcademicController;
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
use App\Http\Controllers\Api\StudentPortalController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public API
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
    Route::post('/contact', [ContactController::class, 'store']);
    Route::post('/enrollments', [EnrollmentController::class, 'store']);
    Route::post('/volunteer', [FeedbackController::class, 'volunteer']);

    // Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Student portal (authenticated)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::prefix('student')->group(function () {
            Route::get('/dashboard', [StudentPortalController::class, 'dashboard']);
            Route::get('/courses', [StudentPortalController::class, 'courses']);
            Route::post('/enrollments', [StudentPortalController::class, 'enroll']);
            Route::get('/courses/{courseId}', [StudentPortalController::class, 'courseDetail']);
            Route::post('/lessons/{lessonId}/progress', [StudentPortalController::class, 'updateLessonProgress']);
            Route::post('/lessons/{lessonId}/complete', [StudentPortalController::class, 'completeLesson']);
            Route::get('/lessons/{lessonId}/quiz', [StudentPortalController::class, 'lessonQuiz']);
            Route::post('/lessons/{lessonId}/quiz', [StudentPortalController::class, 'submitLessonQuiz']);
            Route::post('/error-reports', [FeedbackController::class, 'reportError']);
            Route::get('/schedule', [StudentPortalController::class, 'schedule']);
            Route::get('/assignments', [StudentPortalController::class, 'assignments']);
            Route::post('/assignments/{assignmentId}/submit', [StudentPortalController::class, 'submitAssignment']);
            Route::get('/grades', [StudentPortalController::class, 'grades']);
            Route::get('/certificates/{certificateId}', [StudentPortalController::class, 'showCertificate']);
            Route::get('/profile', [StudentPortalController::class, 'profile']);
            Route::put('/profile', [StudentPortalController::class, 'updateProfile']);
        });
    });
});
