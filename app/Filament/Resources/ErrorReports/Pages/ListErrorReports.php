<?php

namespace App\Filament\Resources\ErrorReports\Pages;

use App\Filament\Resources\ErrorReports\ErrorReportResource;
use Filament\Resources\Pages\ListRecords;

class ListErrorReports extends ListRecords
{
    protected static string $resource = ErrorReportResource::class;
}
