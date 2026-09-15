<?php

namespace App\Http\Controllers\Api;

use App\Enums\LessonMediaType;
use App\Http\Controllers\Api\Concerns\ResolvesAuthenticatedStudent;
use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\GoogleDriveService;
use App\Support\LessonMedia;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentLessonAudioController extends Controller
{
    use ResolvesAuthenticatedStudent;

    public function __construct(private readonly GoogleDriveService $drive) {}

    public function __invoke(Request $request, int $lessonId): StreamedResponse
    {
        $student = $this->student($request);
        $lesson = Lesson::query()->findOrFail($lessonId);

        $this->ensureLessonAccessible($student, $lesson);

        abort_unless($lesson->is_published, 404);

        abort_unless(
            LessonMedia::resolvedMediaType($lesson) === LessonMediaType::Audio,
            422,
            'هذا الدرس ليس من نوع صوت.'
        );

        abort_unless(filled($lesson->google_drive_file_id), 422, 'ملف الصوت غير مُعدّ.');

        if (! $this->drive->isConfigured()) {
            abort(503, 'خدمة الصوت غير متاحة حالياً.');
        }

        try {
            $this->drive->getFileMetadata($lesson->google_drive_file_id);
        } catch (RuntimeException $exception) {
            abort(404, $exception->getMessage());
        }

        $range = $request->header('Range');

        try {
            [$stream, $headers] = $this->drive->openDownloadStream(
                $lesson->google_drive_file_id,
                $lesson->google_drive_resource_key,
                is_string($range) ? $range : null,
            );
        } catch (RuntimeException $exception) {
            abort(502, $exception->getMessage());
        }

        $status = isset($headers['Content-Range']) ? 206 : 200;

        return response()->stream(function () use ($stream): void {
            while (! $stream->eof()) {
                echo $stream->read(8192);
            }
        }, $status, array_merge($headers, [
            'Cache-Control' => 'private, no-store',
        ]));
    }
}
