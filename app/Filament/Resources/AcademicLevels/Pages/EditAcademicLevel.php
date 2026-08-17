<?php

namespace App\Filament\Resources\AcademicLevels\Pages;

use App\Filament\Resources\AcademicLevels\AcademicLevelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcademicLevel extends EditRecord
{
    protected static string $resource = AcademicLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
