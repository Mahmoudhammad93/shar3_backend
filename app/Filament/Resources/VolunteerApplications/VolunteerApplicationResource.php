<?php

namespace App\Filament\Resources\VolunteerApplications;

use App\Filament\Resources\VolunteerApplications\Pages\EditVolunteerApplication;
use App\Filament\Resources\VolunteerApplications\Pages\ListVolunteerApplications;
use App\Models\VolunteerApplication;
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

class VolunteerApplicationResource extends Resource
{
    protected static ?string $model = VolunteerApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandRaised;

    protected static ?string $navigationLabel = 'طلبات التطوع';

    protected static ?string $modelLabel = 'طلب تطوع';

    protected static ?string $pluralModelLabel = 'طلبات التطوع';

    protected static string|\UnitEnum|null $navigationGroup = 'التواصل';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('الاسم')->disabled(),
            TextInput::make('country')->label('الدولة')->disabled(),
            TextInput::make('phone')->label('الهاتف')->disabled(),
            TextInput::make('whatsapp')->label('واتساب')->disabled(),
            TextInput::make('work_type')->label('نوع العمل')->disabled(),
            Textarea::make('experience')->label('الخبرة')->disabled()->columnSpanFull(),
            Select::make('status')
                ->label('الحالة')
                ->options([
                    'new' => 'جديد',
                    'reviewing' => 'قيد المراجعة',
                    'accepted' => 'مقبول',
                    'rejected' => 'مرفوض',
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
                TextColumn::make('name')->label('الاسم')->searchable(),
                TextColumn::make('country')->label('الدولة'),
                TextColumn::make('work_type')->label('نوع العمل')->limit(40),
                TextColumn::make('status')->label('الحالة')->badge(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVolunteerApplications::route('/'),
            'edit' => EditVolunteerApplication::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = VolunteerApplication::query()->where('status', 'new')->count();

        return $count > 0 ? (string) $count : null;
    }
}
