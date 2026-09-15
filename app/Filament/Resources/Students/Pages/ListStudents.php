<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->with(['academicLevel', 'academicYear', 'currentSemester']);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('إضافة طالب')
                ->modalHeading('إضافة طالب')
                ->modalWidth(Width::SevenExtraLarge)
                ->createAnother(false)
                ->mutateFormDataUsing(function (array $data): array {
                    if (empty($data['name'])) {
                        $data['name'] = trim(($data['first_name'] ?? '').' '.($data['last_name'] ?? ''));
                    }

                    return $data;
                }),
        ];
    }
}
