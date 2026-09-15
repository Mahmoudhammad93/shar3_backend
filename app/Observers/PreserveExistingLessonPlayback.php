<?php

namespace App\Observers;

use App\Models\Lesson;

/**
 * Existing lesson playback columns are frozen unless an admin attaches a
 * different, non-empty Bunny video id. Status-only updates are allowed
 * after that id already exists. video_url is never rewritten.
 */
class PreserveExistingLessonPlayback
{
    public function updating(Lesson $lesson): void
    {
        $originalVideoId = $lesson->getOriginal('bunny_video_id');
        $incomingVideoId = $lesson->bunny_video_id;
        $explicitNewVideo = filled($incomingVideoId) && $incomingVideoId !== $originalVideoId;

        if ($lesson->isDirty('video_url')) {
            $lesson->video_url = $lesson->getOriginal('video_url');
        }

        if ($explicitNewVideo) {
            return;
        }

        $statusOnly = $lesson->isDirty('bunny_status')
            && filled($originalVideoId)
            && ! $lesson->isDirty('bunny_video_id')
            && ! $lesson->isDirty('bunny_library_id')
            && ! $lesson->isDirty('video_provider')
            && ! $lesson->isDirty('media_type')
            && ! $lesson->isDirty('google_drive_file_id')
            && ! $lesson->isDirty('google_drive_resource_key');

        foreach ([
            'media_type',
            'video_provider',
            'bunny_library_id',
            'bunny_video_id',
            'bunny_status',
            'google_drive_file_id',
            'google_drive_resource_key',
        ] as $field) {
            if ($field === 'bunny_status' && $statusOnly) {
                continue;
            }

            if ($lesson->isDirty($field)) {
                $lesson->{$field} = $lesson->getOriginal($field);
            }
        }
    }
}
