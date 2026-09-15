<?php

namespace App\Filament\Resources\Lessons\Pages\Concerns;

use App\Enums\LessonMediaType;
use App\Models\Lesson;

trait NormalizesLessonMedia
{
    /** @param  array<string, mixed>  $data */
    protected function normalizeLessonMedia(array $data): array
    {
        $record = property_exists($this, 'record') ? $this->record : null;

        if ($record instanceof Lesson && $record->exists) {
            return $this->preserveExistingLessonPlayback($record, $data);
        }

        $mediaType = $data['media_type'] ?? LessonMediaType::Text->value;
        $data['media_type'] = $mediaType;

        if ($mediaType === LessonMediaType::Video->value && filled($data['bunny_video_id'] ?? null)) {
            $data['video_provider'] = $data['video_provider'] ?? 'bunny';
        }

        return $data;
    }

    /**
     * Ordinary edits must not rewrite playback columns. A new Bunny video id
     * is stored only after the admin explicitly uploaded that file.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function preserveExistingLessonPlayback(Lesson $record, array $data): array
    {
        $incomingVideoId = $data['bunny_video_id'] ?? null;
        $explicitNewVideo = filled($incomingVideoId) && $incomingVideoId !== $record->bunny_video_id;

        if (! $explicitNewVideo) {
            $data['media_type'] = $record->media_type;
            $data['video_provider'] = $record->video_provider;
            $data['bunny_library_id'] = $record->bunny_library_id;
            $data['bunny_video_id'] = $record->bunny_video_id;
            $data['bunny_status'] = $record->bunny_status;
            $data['google_drive_file_id'] = $record->google_drive_file_id;
            $data['google_drive_resource_key'] = $record->google_drive_resource_key;
        }

        $data['video_url'] = $record->video_url;

        return $data;
    }
}
