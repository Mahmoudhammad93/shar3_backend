<?php

namespace App\Filament\Resources\AcademicLevels\Pages;

use App\Filament\Resources\AcademicLevels\AcademicLevelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAcademicLevels extends ListRecords
{
    protected static string $resource = AcademicLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
