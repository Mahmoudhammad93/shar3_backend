<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\FaqResource;
use App\Http\Resources\HeroSlideResource;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\SettingResource;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\TestimonialResource;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\Program;
use App\Models\SiteSetting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'settings' => new SettingResource(SiteSetting::current()),
            'hero_slides' => HeroSlideResource::collection(
                HeroSlide::query()->where('is_active', true)->orderBy('sort_order')->get()
            ),
            'featured_courses' => CourseResource::collection(
                Course::query()
                    ->with(['category', 'teacher'])
                    ->where('is_published', true)
                    ->where('is_featured', true)
                    ->orderBy('sort_order')
                    ->limit(6)
                    ->get()
            ),
            'programs' => ProgramResource::collection(
                Program::query()->where('is_active', true)->orderBy('sort_order')->limit(4)->get()
            ),
            'teachers' => TeacherResource::collection(
                Teacher::query()->where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->limit(4)->get()
            ),
            'testimonials' => TestimonialResource::collection(
                Testimonial::query()->where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->limit(6)->get()
            ),
            'announcements' => AnnouncementResource::collection(
                Announcement::query()
                    ->where('is_published', true)
                    ->orderByDesc('published_at')
                    ->limit(3)
                    ->get()
            ),
            'faqs' => FaqResource::collection(
                Faq::query()->where('is_active', true)->orderBy('sort_order')->limit(6)->get()
            ),
            'stats' => [
                'students' => Student::query()->count(),
                'courses' => Course::query()->where('is_published', true)->count(),
                'teachers' => Teacher::query()->where('is_active', true)->count(),
                'programs' => Program::query()->where('is_active', true)->count(),
                'categories' => Category::query()->where('is_active', true)->count(),
                'graduates' => Student::query()->where('status', Student::STATUS_GRADUATED)->count(),
            ],
        ]);
    }
}
