<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LessonBunnyStatus;
use App\Enums\LessonMediaType;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\BunnyStreamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class LessonBunnyMediaController extends Controller
{
    public function __construct(private readonly BunnyStreamService $bunny) {}

    public function prepare(Request $request): JsonResponse
    {
        $title = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
        ])['title'] ?? 'درس';

        return $this->issueDirectUpload($title);
    }

    public function replace(Request $request, Lesson $lesson): JsonResponse
    {
        $this->assertVideoLesson($lesson);

        $title = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
        ])['title'] ?? $lesson->title_ar;

        return $this->issueDirectUpload($title);
    }

    public function confirm(Request $request, Lesson $lesson): JsonResponse
    {
        $videoId = $request->validate([
            'video_id' => ['required', 'string', 'max:255'],
        ])['video_id'];

        $this->assertPending($videoId);
        $this->assertVideoLesson($lesson);

        if (! $this->bunny->isConfigured()) {
            return response()->json(['message' => 'Bunny Stream غير مُعدّ على الخادم.'], 503);
        }

        try {
            $remote = $this->bunny->getVideo($videoId);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 502);
        }

        $mapped = $this->bunny->mapRemoteStatus(
            isset($remote['status']) ? (int) $remote['status'] : null
        );

        if ($mapped === LessonBunnyStatus::Created) {
            $mapped = LessonBunnyStatus::Processing;
        }

        if ($mapped === LessonBunnyStatus::Failed) {
            return response()->json([
                'message' => 'فشل رفع الفيديو على Bunny ولم يُحدَّث الدرس.',
                'bunny_status' => LessonBunnyStatus::Failed->value,
            ], 422);
        }

        $lesson->update([
            'media_type' => LessonMediaType::Video->value,
            'video_provider' => 'bunny',
            'bunny_library_id' => $this->bunny->libraryId(),
            'bunny_video_id' => $videoId,
            'bunny_status' => $mapped->value,
        ]);

        return response()->json([
            'message' => 'تم ربط الفيديو بالدرس.',
            'video_id' => $videoId,
            'bunny_status' => $lesson->fresh()->bunny_status,
            'status_label' => LessonBunnyStatus::tryFrom((string) $lesson->fresh()->bunny_status)?->labelAr(),
        ]);
    }

    public function cancel(Request $request): JsonResponse
    {
        $videoId = $request->validate([
            'video_id' => ['required', 'string', 'max:255'],
        ])['video_id'];

        $this->assertPending($videoId);

        if (Lesson::query()->where('bunny_video_id', $videoId)->exists()) {
            return response()->json(['message' => 'الفيديو مرتبط بدرس ولن يُحذف.'], 422);
        }

        if ($this->bunny->isConfigured()) {
            try {
                $this->bunny->deleteVideo($videoId);
            } catch (RuntimeException $exception) {
                return response()->json(['message' => $exception->getMessage()], 502);
            }
        }

        return response()->json(['message' => 'تم إلغاء الرفع.']);
    }

    public function create(Lesson $lesson): JsonResponse
    {
        $this->assertVideoLesson($lesson);

        if ($lesson->bunny_video_id) {
            return response()->json([
                'message' => 'يوجد فيديو Bunny بالفعل. ارفع ملفاً جديداً فقط إذا أردت استبداله.',
                'video_id' => $lesson->bunny_video_id,
                'bunny_status' => $lesson->bunny_status,
            ]);
        }

        return $this->issueDirectUpload($lesson->title_ar);
    }

    public function upload(Request $request, string $videoId): JsonResponse
    {
        $this->assertPending($videoId);

        if (! $this->bunny->isConfigured()) {
            return response()->json(['message' => 'Bunny Stream غير مُعدّ على الخادم.'], 503);
        }

        $contents = $request->getContent();

        if ($contents === '') {
            return response()->json(['message' => 'ملف الفيديو فارغ.'], 422);
        }

        try {
            $this->bunny->uploadVideo($videoId, $contents);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 502);
        }

        return response()->json([
            'message' => 'تم رفع الفيديو إلى Bunny.',
            'video_id' => $videoId,
        ]);
    }

    public function status(Lesson $lesson): JsonResponse
    {
        $this->assertVideoLesson($lesson);

        if (! $lesson->bunny_video_id) {
            return response()->json([
                'bunny_status' => $lesson->bunny_status,
                'status_label' => LessonBunnyStatus::Created->labelAr(),
            ]);
        }

        if (! $this->bunny->isConfigured()) {
            return response()->json(['message' => 'Bunny Stream غير مُعدّ على الخادم.'], 503);
        }

        try {
            $remote = $this->bunny->getVideo($lesson->bunny_video_id);
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 502);
        }

        $mapped = $this->bunny->mapRemoteStatus(
            isset($remote['status']) ? (int) $remote['status'] : null
        );

        return response()->json([
            'bunny_status' => $mapped->value,
            'status_label' => $mapped->labelAr(),
            'remote' => [
                'status' => $remote['status'] ?? null,
                'encode_progress' => $remote['encodeProgress'] ?? null,
            ],
            'embed_url' => $this->bunny->embedUrl($lesson->bunny_video_id),
        ]);
    }

    public function destroy(Lesson $lesson): JsonResponse
    {
        return response()->json([
            'message' => 'حذف فيديو Bunny غير متاح في هذه النسخة حتى لا تُفقد الوسائط الحالية.',
        ], 422);
    }

    private function issueDirectUpload(string $title): JsonResponse
    {
        if (! $this->bunny->isConfigured()) {
            return response()->json(['message' => 'Bunny Stream غير مُعدّ على الخادم.'], 503);
        }

        try {
            $remote = $this->bunny->createVideo($title !== '' ? $title : 'درس');
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 502);
        }

        $videoId = $remote['guid'] ?? $remote['videoId'] ?? null;

        if (! is_string($videoId) || $videoId === '') {
            return response()->json(['message' => 'استجابة Bunny Stream غير متوقعة.'], 502);
        }

        $pending = session('bunny_pending_video_ids', []);
        $pending[] = $videoId;
        session(['bunny_pending_video_ids' => array_values(array_unique($pending))]);

        $upload = $this->bunny->tusUploadAuthorization($videoId);

        return response()->json([
            'message' => 'تم تجهيز الرفع المباشر.',
            'video_id' => $videoId,
            'upload' => $upload,
        ]);
    }

    private function assertPending(string $videoId): void
    {
        abort_unless(
            in_array($videoId, session('bunny_pending_video_ids', []), true),
            422,
            'معرّف الفيديو غير مصرح لهذا الطلب.'
        );
    }

    private function assertVideoLesson(Lesson $lesson): void
    {
        abort_unless(
            in_array($lesson->media_type, [LessonMediaType::Video->value, null], true),
            422,
            'الدرس ليس من نوع فيديو.'
        );
    }
}
