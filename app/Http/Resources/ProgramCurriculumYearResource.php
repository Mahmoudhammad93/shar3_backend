<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ProgramCurriculumYearResource extends JsonResource
{
    /** @param  array<string, mixed>  $resource */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        /** @var array<string, mixed> $year */
        $year = $this->resource;

        /** @var Collection<int, array<string, mixed>> $semesters */
        $semesters = collect($year['semesters'] ?? []);

        return [
            'id' => $year['id'],
            'name_ar' => $year['name_ar'],
            'name_en' => $year['name_en'] ?? null,
            'slug' => $year['slug'],
            'year_number' => $year['year_number'],
            'semesters' => $semesters
                ->map(fn (array $semester) => [
                    'id' => $semester['id'],
                    'name_ar' => $semester['name_ar'],
                    'name_en' => $semester['name_en'] ?? null,
                    'slug' => $semester['slug'],
                    'semester_number' => $semester['semester_number'],
                    'subjects' => CurriculumSubjectResource::collection($semester['subjects']),
                ])
                ->values()
                ->all(),
        ];
    }
}
