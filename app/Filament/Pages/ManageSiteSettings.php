<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\AdminPanelSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSiteSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'إعدادات الموقع';

    protected static ?string $title = 'إعدادات الموقع';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->toArray());
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('الهوية')->schema([
                    TextInput::make('site_name_ar')->label('اسم الموقع (عربي)')->required(),
                    TextInput::make('site_name_en')->label('اسم الموقع (إنجليزي)'),
                    TextInput::make('tagline_ar')->label('الشعار (عربي)'),
                    TextInput::make('tagline_en')->label('الشعار (إنجليزي)'),
                    FileUpload::make('logo')->label('الشعار')->image()->directory('settings'),
                    FileUpload::make('favicon')->label('أيقونة الموقع')->image()->directory('settings'),
                ])->columns(2),
                Section::make('عن المعهد')->schema([
                    Textarea::make('about_ar')->label('نبذة (عربي)')->rows(4),
                    Textarea::make('about_en')->label('نبذة (إنجليزي)')->rows(4),
                    Textarea::make('vision_ar')->label('الرؤية (عربي)')->rows(3),
                    Textarea::make('vision_en')->label('الرؤية (إنجليزي)')->rows(3),
                    Textarea::make('mission_ar')->label('الرسالة (عربي)')->rows(3),
                    Textarea::make('mission_en')->label('الرسالة (إنجليزي)')->rows(3),
                ])->columns(2),
                Section::make('التواصل')->schema([
                    TextInput::make('phone')->label('الهاتف')->tel(),
                    TextInput::make('email')->label('البريد')->email(),
                    TextInput::make('whatsapp')->label('واتساب'),
                    TextInput::make('address_ar')->label('العنوان (عربي)'),
                    TextInput::make('address_en')->label('العنوان (إنجليزي)'),
                ])->columns(2),
                Section::make('وسائل التواصل')->schema([
                    TextInput::make('facebook')->label('فيسبوك')->url(),
                    TextInput::make('twitter')->label('تويتر')->url(),
                    TextInput::make('instagram')->label('انستغرام')->url(),
                    TextInput::make('youtube')->label('يوتيوب')->url(),
                ])->columns(2),
                Section::make('التذييل')->schema([
                    Textarea::make('footer_text_ar')->label('نص التذييل (عربي)')->rows(2),
                    Textarea::make('footer_text_en')->label('نص التذييل (إنجليزي)')->rows(2),
                ])->columns(2),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    EmbeddedSchema::make('form'),
                ])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('حفظ الإعدادات')
                                ->submit('save'),
                        ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        SiteSetting::current()->update($data);
        AdminPanelSettings::forgetCache();

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
