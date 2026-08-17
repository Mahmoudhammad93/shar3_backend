<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageRegulations extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static ?string $navigationLabel = 'اللائحة التنظيمية';

    protected static ?string $title = 'إدارة اللائحة التنظيمية';

    protected static string|\UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'regulations';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current();

        $this->form->fill([
            'regulations_ar' => $settings->regulations_ar,
            'regulations_en' => $settings->regulations_en,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('معاينة في الموقع')
                ->icon(Heroicon::OutlinedEye)
                ->url(rtrim(config('app.frontend_url'), '/').'/regulations/')
                ->openUrlInNewTab(),
        ];
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('محتوى اللائحة')
                    ->description('يظهر في صفحة اللائحة التنظيمية على الموقع')
                    ->schema([
                        RichEditor::make('regulations_ar')
                            ->label('اللائحة (عربي)')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('regulations')
                            ->fileAttachmentsDisk('public')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'link'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList', 'blockquote'],
                                ['attachFiles', 'undo', 'redo'],
                            ]),
                        RichEditor::make('regulations_en')
                            ->label('اللائحة (إنجليزي)')
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('regulations')
                            ->fileAttachmentsDisk('public')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'link'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList', 'blockquote'],
                                ['attachFiles', 'undo', 'redo'],
                            ]),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    EmbeddedSchema::make('form'),
                ])
                    ->id('regulations-form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('حفظ اللائحة')
                                ->submit('save'),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::current()->update([
            'regulations_ar' => $data['regulations_ar'] ?? null,
            'regulations_en' => $data['regulations_en'] ?? null,
        ]);

        Notification::make()
            ->title('تم حفظ اللائحة التنظيمية')
            ->success()
            ->send();
    }
}
