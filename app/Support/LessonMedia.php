<?php

namespace App\Support;

use App\Enums\LessonMediaType;
use App\Models\Lesson;

class LessonMedia
{
    public static function resolvedMediaType(Lesson $lesson): LessonMediaType
    {
        if ($lesson->media_type !== null) {
            return LessonMediaType::from($lesson->media_type);
        }

        if ($lesson->video_url || ($lesson->video_provider === 'bunny' && $lesson->bunny_video_id)) {
            return LessonMediaType::Video;
        }

        return LessonMediaType::Text;
    }

    public static function requiresVideoProgress(Lesson $lesson): bool
    {
        return self::resolvedMediaType($lesson) === LessonMediaType::Video;
    }

    /** @return array<string, mixed> */
    public static function playbackPayload(Lesson $lesson): array
    {
        $mediaType = self::resolvedMediaType($lesson);

        $payload = [
            'media_type' => $mediaType->value,
        ];

        if ($mediaType === LessonMediaType::Video) {
            $payload['video_provider'] = $lesson->video_provider;
            $payload['video_url'] = $lesson->video_url;

            if ($lesson->video_provider === 'bunny' && $lesson->bunny_video_id) {
                $bunny = app(\App\Services\BunnyStreamService::class);
                $payload['bunny'] = [
                    'library_id' => $lesson->bunny_library_id ?? $bunny->libraryId(),
                    'video_id' => $lesson->bunny_video_id,
                    'status' => $lesson->bunny_status,
                    'embed_url' => $bunny->embedUrl($lesson->bunny_video_id),
                    'player_url' => $bunny->playerUrl($lesson->bunny_video_id),
                    'playback_url' => $lesson->bunny_status === 'ready'
                        ? $bunny->playbackUrl($lesson->bunny_video_id)
                        : null,
                ];
            }
        }

        if ($mediaType === LessonMediaType::Audio) {
            $payload['audio'] = [
                'url' => url("/api/v1/student/lessons/{$lesson->id}/audio"),
            ];
        }

        return $payload;
    }

    public static function legacyOrNullVideoUrl(Lesson $lesson): ?string
    {
        return $lesson->video_url;
    }
}
