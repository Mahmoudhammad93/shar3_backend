<?php

namespace App\Filament\Resources\ErrorReports;

use App\Filament\Resources\ErrorReports\Pages\EditErrorReport;
use App\Filament\Resources\ErrorReports\Pages\ListErrorReports;
use App\Models\ErrorReport;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ErrorReportResource extends Resource
{
    protected static ?string $model = ErrorReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'بلاغات الأخطاء';

    protected static ?string $modelLabel = 'بلاغ';

    protected static ?string $pluralModelLabel = 'بلاغات الأخطاء';

    protected static string|\UnitEnum|null $navigationGroup = 'التواصل';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('error_type')->label('نوع الخطأ')->disabled(),
            TextInput::make('page_url')->label('الصفحة')->disabled(),
            Textarea::make('description')->label('الوصف')->disabled()->columnSpanFull(),
            Select::make('status')
                ->label('الحالة')
                ->options([
                    'new' => 'جديد',
                    'reviewing' => 'قيد المراجعة',
                    'resolved' => 'تم الحل',
                ])
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('التاريخ')->dateTime()->sortable(),
                TextColumn::make('student.name')->label('الطالب')->placeholder('زائر'),
                TextColumn::make('error_type')->label('النوع'),
                TextColumn::make('description')->label('الوصف')->limit(60),
                TextColumn::make('status')->label('الحالة')->badge(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListErrorReports::route('/'),
            'edit' => EditErrorReport::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ErrorReport::query()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }
}
