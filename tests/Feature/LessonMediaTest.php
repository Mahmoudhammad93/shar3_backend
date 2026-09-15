<?php

namespace Tests\Feature;

use App\Enums\LessonMediaType;
use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\BunnyStreamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LessonMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_lesson_can_store_media_type_fields(): void
    {
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'media-course',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'درس صوت',
            'media_type' => LessonMediaType::Audio->value,
            'google_drive_file_id' => 'drive-file-123',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->assertSame('audio', $lesson->fresh()->media_type);
        $this->assertSame('drive-file-123', $lesson->google_drive_file_id);
    }

    public function test_legacy_video_url_lesson_remains_compatible(): void
    {
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'legacy-course',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'درس قديم',
            'video_url' => 'https://example.com/video.mp4',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->assertSame('video', \App\Support\LessonMedia::resolvedMediaType($lesson)->value);
        $this->assertSame('https://example.com/video.mp4', \App\Support\LessonMedia::legacyOrNullVideoUrl($lesson));
    }

    public function test_bunny_service_sends_access_key_header(): void
    {
        config([
            'services.bunny_stream.library_id' => '12345',
            'services.bunny_stream.api_key' => 'test-stream-key',
        ]);

        Http::fake([
            'video.bunnycdn.com/library/12345/videos' => Http::response(['guid' => 'vid-1'], 200),
        ]);

        $service = app(BunnyStreamService::class);
        $result = $service->createVideo('Test');

        $this->assertSame('vid-1', $result['guid']);

        Http::assertSent(function ($request) {
            return $request->hasHeader('AccessKey', 'test-stream-key');
        });
    }

    public function test_bunny_webhook_updates_matching_lesson(): void
    {
        config([
            'services.bunny_stream.library_id' => '1',
            'services.bunny_stream.api_key' => 'signing-secret',
        ]);

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'bunny-course',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'درس',
            'media_type' => LessonMediaType::Video->value,
            'video_provider' => 'bunny',
            'bunny_video_id' => 'bunny-guid-1',
            'bunny_status' => 'processing',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $payload = json_encode(['VideoGuid' => 'bunny-guid-1', 'Status' => 3]);
        $signature = hash_hmac('sha256', $payload, 'signing-secret');

        $this->call(
            'POST',
            '/api/v1/webhooks/bunny-stream',
            server: $this->transformHeadersToServerVars([
                'X-BunnyStream-Signature' => $signature,
                'X-BunnyStream-Signature-Version' => 'v1',
                'X-BunnyStream-Signature-Algorithm' => 'hmac-sha256',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]),
            content: $payload,
        )->assertOk();

        $this->assertSame('ready', $lesson->fresh()->bunny_status);
    }

    public function test_unauthorized_student_cannot_access_audio_lesson(): void
    {
        $this->actingAsStudent();

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'locked-audio',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'صوت',
            'media_type' => LessonMediaType::Audio->value,
            'google_drive_file_id' => 'file-1',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->getJson("/api/v1/student/lessons/{$lesson->id}/audio")->assertForbidden();
    }

    public function test_audio_lesson_without_drive_file_is_rejected_for_enrolled_student(): void
    {
        $student = $this->actingAsStudent();
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'audio-course',
            'is_published' => true,
        ]);

        Enrollment::query()->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_APPROVED,
            'enrolled_at' => now(),
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'صوت',
            'media_type' => LessonMediaType::Audio->value,
            'google_drive_file_id' => null,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->getJson("/api/v1/student/lessons/{$lesson->id}/audio")->assertStatus(422);
    }

    public function test_text_lesson_remains_valid_without_media_fields(): void
    {
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'text-course',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'نص',
            'media_type' => LessonMediaType::Text->value,
            'content_ar' => 'محتوى',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->assertSame('text', \App\Support\LessonMedia::resolvedMediaType($lesson)->value);
        $this->assertFalse(\App\Support\LessonMedia::requiresVideoProgress($lesson));
    }

    public function test_staff_can_initiate_bunny_create_for_video_lesson(): void
    {
        config([
            'services.bunny_stream.library_id' => '99',
            'services.bunny_stream.api_key' => 'key',
        ]);

        Http::fake([
            'video.bunnycdn.com/library/99/videos' => Http::response(['guid' => 'new-video'], 200),
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'staff-bunny',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'فيديو',
            'media_type' => LessonMediaType::Video->value,
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/admin/lessons/{$lesson->id}/bunny/create")
            ->assertOk()
            ->assertJsonPath('video_id', 'new-video');

        $this->assertStringNotContainsString((string) config('services.bunny_stream.api_key'), $response->getContent());
        $this->assertNull($lesson->fresh()->bunny_video_id);
        $this->assertNull($lesson->fresh()->video_url);
    }

    public function test_direct_upload_prepare_does_not_expose_bunny_api_key(): void
    {
        config([
            'services.bunny_stream.library_id' => '99',
            'services.bunny_stream.api_key' => 'super-secret-stream-key',
        ]);

        Http::fake([
            'video.bunnycdn.com/library/99/videos' => Http::response(['guid' => 'pending-video'], 200),
        ]);

        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)
            ->postJson('/admin/bunny/uploads', ['title' => 'درس'])
            ->assertOk()
            ->assertJsonPath('video_id', 'pending-video')
            ->assertJsonPath('upload.endpoint', 'https://video.bunnycdn.com/tusupload');

        $this->assertStringNotContainsString('super-secret-stream-key', $response->getContent());
    }

    public function test_unauthorized_users_cannot_start_bunny_upload(): void
    {
        $this->postJson('/admin/bunny/uploads', ['title' => 'درس'])->assertUnauthorized();

        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student)
            ->postJson('/admin/bunny/uploads', ['title' => 'درس'])
            ->assertForbidden();
    }

    public function test_confirm_sets_processing_and_does_not_mark_failed_upload_ready(): void
    {
        config([
            'services.bunny_stream.library_id' => '99',
            'services.bunny_stream.api_key' => 'key',
        ]);

        Http::fake([
            'video.bunnycdn.com/library/99/videos/good-video' => Http::response(['status' => 1], 200),
            'video.bunnycdn.com/library/99/videos/bad-video' => Http::response(['status' => 5], 200),
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'confirm-course',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'فيديو',
            'media_type' => LessonMediaType::Video->value,
            'video_provider' => 'bunny',
            'bunny_video_id' => 'old-video',
            'bunny_status' => 'ready',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($user)->withSession([
            'bunny_pending_video_ids' => ['good-video', 'bad-video'],
        ]);

        $this->postJson("/admin/lessons/{$lesson->id}/bunny/confirm", [
            'video_id' => 'bad-video',
        ])->assertStatus(422);

        $this->assertSame('old-video', $lesson->fresh()->bunny_video_id);
        $this->assertSame('ready', $lesson->fresh()->bunny_status);

        $this->postJson("/admin/lessons/{$lesson->id}/bunny/confirm", [
            'video_id' => 'good-video',
        ])->assertOk()->assertJsonPath('bunny_status', 'processing');

        $this->assertSame('good-video', $lesson->fresh()->bunny_video_id);
        $this->assertNotSame('ready', $lesson->fresh()->bunny_status);
    }

    public function test_lesson_api_payload_does_not_include_bunny_api_key(): void
    {
        config([
            'services.bunny_stream.library_id' => '99',
            'services.bunny_stream.api_key' => 'super-secret-stream-key',
            'services.bunny_stream.cdn_hostname' => 'video.example.com',
        ]);

        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'api-bunny',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'فيديو',
            'media_type' => LessonMediaType::Video->value,
            'video_provider' => 'bunny',
            'bunny_library_id' => '99',
            'bunny_video_id' => 'play-me',
            'bunny_status' => 'ready',
            'video_url' => 'https://example.com/legacy.mp4',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $resolved = LessonResource::make($lesson)->resolve();
        $payload = json_encode($resolved);

        $this->assertIsString($payload);
        $this->assertStringNotContainsString('super-secret-stream-key', $payload);
        $this->assertStringContainsString('play-me', $payload);
        $this->assertSame('https://example.com/legacy.mp4', $resolved['video_url']);
        $this->assertSame(
            'https://player.mediadelivery.net/embed/99/play-me',
            $resolved['bunny']['player_url']
        );
    }

    public function test_bunny_uploader_view_includes_file_picker_label(): void
    {
        $html = view('filament.forms.components.bunny-video-uploader')->render();

        $this->assertStringContainsString('اختر ملف الفيديو', $html);
        $this->assertStringContainsString('type="file"', $html);
        $this->assertStringContainsString('.mp4,.webm,.mov', $html);
    }

    public function test_upload_video_sends_binary_put_without_exposing_api_key(): void
    {
        config([
            'services.bunny_stream.library_id' => '99',
            'services.bunny_stream.api_key' => 'server-only-key',
        ]);

        Http::fake([
            'video.bunnycdn.com/library/99/videos/vid-1' => Http::response(['success' => true], 200),
        ]);

        app(BunnyStreamService::class)->uploadVideo('vid-1', 'binary-bytes');

        Http::assertSent(function ($request) {
            return $request->method() === 'PUT'
                && str_contains($request->url(), '/library/99/videos/vid-1')
                && $request->body() === 'binary-bytes'
                && $request->hasHeader('AccessKey', 'server-only-key')
                && str_contains((string) $request->header('Content-Type')[0], 'application/octet-stream');
        });
    }

    public function test_failed_upload_does_not_change_existing_lesson(): void
    {
        config([
            'services.bunny_stream.library_id' => '99',
            'services.bunny_stream.api_key' => 'server-only-key',
        ]);

        Http::fake([
            'video.bunnycdn.com/library/99/videos/pending-video' => Http::response(['message' => 'no'], 500),
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'failed-upload',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'قديم',
            'video_url' => 'https://example.com/keep.mp4',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($user)->withSession([
            'bunny_pending_video_ids' => ['pending-video'],
        ])->call('PUT', '/admin/bunny/uploads/pending-video/content', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'CONTENT_TYPE' => 'application/octet-stream',
        ], 'binary-bytes')->assertStatus(502);

        $fresh = $lesson->fresh();
        $this->assertSame('https://example.com/keep.mp4', $fresh->video_url);
        $this->assertNull($fresh->bunny_video_id);
        $this->assertNull($fresh->media_type);
    }

    public function test_editing_existing_lesson_without_new_video_does_not_change_playback_fields(): void
    {
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'preserve-edit',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'قديم',
            'video_url' => 'https://example.com/keep.mp4',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $lesson->update([
            'title_ar' => 'عنوان محدّث',
            'media_type' => LessonMediaType::Text->value,
            'video_url' => null,
            'video_provider' => 'bunny',
            'bunny_video_id' => null,
            'bunny_status' => 'ready',
        ]);

        $fresh = $lesson->fresh();
        $this->assertSame('عنوان محدّث', $fresh->title_ar);
        $this->assertSame('https://example.com/keep.mp4', $fresh->video_url);
        $this->assertNull($fresh->media_type);
        $this->assertNull($fresh->bunny_video_id);
        $this->assertNull($fresh->video_provider);
    }

    public function test_successful_new_video_keeps_existing_video_url_and_does_not_delete_old_bunny_video(): void
    {
        config([
            'services.bunny_stream.library_id' => '99',
            'services.bunny_stream.api_key' => 'key',
        ]);

        Http::fake([
            'video.bunnycdn.com/library/99/videos/good-video' => Http::response(['status' => 1], 200),
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        $course = Course::query()->create([
            'title_ar' => 'دورة',
            'slug' => 'keep-legacy',
            'is_published' => true,
        ]);

        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'title_ar' => 'فيديو',
            'video_url' => 'https://example.com/legacy.mp4',
            'video_provider' => 'bunny',
            'bunny_video_id' => 'old-video',
            'bunny_status' => 'ready',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        $this->actingAs($user)->withSession([
            'bunny_pending_video_ids' => ['good-video'],
        ])->postJson("/admin/lessons/{$lesson->id}/bunny/confirm", [
            'video_id' => 'good-video',
        ])->assertOk();

        $fresh = $lesson->fresh();
        $this->assertSame('good-video', $fresh->bunny_video_id);
        $this->assertSame('https://example.com/legacy.mp4', $fresh->video_url);

        Http::assertNotSent(fn ($request) => $request->method() === 'DELETE');
    }

    public function test_media_migration_is_additive_and_does_not_rewrite_rows(): void
    {
        $path = database_path('migrations/2026_09_13_120000_add_media_fields_to_lessons_table.php');
        $source = file_get_contents($path);

        $this->assertIsString($source);
        $this->assertStringContainsString("->nullable()", $source);
        $this->assertDoesNotMatchRegularExpression('/\b(update|delete|truncate)\b/i', $source);
        $this->assertStringNotContainsString('DB::table', $source);
    }
}
