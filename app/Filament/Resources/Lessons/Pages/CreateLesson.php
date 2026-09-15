<?php

namespace App\Filament\Resources\Lessons\Pages;

use App\Filament\Resources\Lessons\LessonResource;
use App\Filament\Resources\Lessons\Pages\Concerns\NormalizesLessonMedia;
use Filament\Resources\Pages\CreateRecord;

class CreateLesson extends CreateRecord
{
    use NormalizesLessonMedia;

    protected static string $resource = LessonResource::class;

    /** @param  array<string, mixed>  $data */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeLessonMedia($data);
    }
}
