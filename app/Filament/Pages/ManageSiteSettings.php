<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\AdminPanelSettings;
use App\Support\WebsiteColorPalettes;
use App\Support\WebsiteNavPages;
use Filament\Actions\Action;
use App\Support\HexColor;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
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
        $data = SiteSetting::current()->toArray();
        $data['website_nav_pages'] = WebsiteNavPages::resolve($data['website_nav_pages'] ?? null);
        $resolvedPalette = WebsiteColorPalettes::resolveStoredPalette($data);
        if ($resolvedPalette !== ($data['website_color_palette'] ?? null)) {
            SiteSetting::current()->update(['website_color_palette' => $resolvedPalette]);
            AdminPanelSettings::forgetCache();
        }
        $data['website_color_palette'] = $resolvedPalette;
        $this->form->fill($data);
    }

    public function applyWebsiteColorPalette(string $paletteId): void
    {
        if ($paletteId === 'custom') {
            $this->data['website_color_palette'] = 'custom';

            return;
        }

        if (! WebsiteColorPalettes::get($paletteId)) {
            return;
        }

        $updated = WebsiteColorPalettes::apply(
            array_merge($this->data ?? [], ['website_color_palette' => $paletteId]),
            $paletteId,
        );

        $this->data = $updated;
        $this->form->fill($updated);

        SiteSetting::current()->update([
            'website_color_palette' => $updated['website_color_palette'],
            'website_primary_color' => $updated['website_primary_color'],
            'website_accent_color' => $updated['website_accent_color'],
            'website_background_color' => $updated['website_background_color'],
        ]);
        AdminPanelSettings::forgetCache();
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
                    FileUpload::make('logo')->label('الشعار')->image()->directory('settings')
                        ->helperText('يُفضّل شعار بخلفية شفافة أو بيضاء — يظهر في الهيدر والصفحة الرئيسية'),
                    FileUpload::make('favicon')->label('أيقونة الموقع')->image()->directory('settings'),
                ])->columns(2),
                Section::make('ألوان الموقع')->schema([
                    Hidden::make('website_color_palette')
                        ->default('institute_navy_gold'),
                    View::make('filament.forms.color-palette-picker')
                        ->viewData(fn (Get $get): array => [
                            'palettes' => WebsiteColorPalettes::list(),
                            'selected' => $get('website_color_palette') ?? 'custom',
                            'applyMethod' => 'applyWebsiteColorPalette',
                            'heading' => 'لوحات ألوان الموقع',
                            'description' => 'اختر لوحة ألوان جاهزة أو عدّل الألوان يدوياً — تُطبَّق على الصفحة الرئيسية وجميع صفحات الموقع.',
                        ])
                        ->columnSpanFull(),
                    ColorPicker::make('website_primary_color')
                        ->label('اللون الأساسي')
                        ->default('#002B5B')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('website_color_palette', 'custom')),
                    ColorPicker::make('website_accent_color')
                        ->label('لون التمييز (ذهبي)')
                        ->default('#C5A04D')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('website_color_palette', 'custom')),
                    ColorPicker::make('website_background_color')
                        ->label('لون الخلفية')
                        ->default('#f7f9fc')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('website_color_palette', 'custom')),
                ])->columns(3),
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
                    TextInput::make('telegram')->label('تليجرام')->url(),
                ])->columns(2),
                Section::make('صفحات القائمة (النافبار)')
                    ->description('عدّل اسم ووصف كل صفحة، ورتّب ظهورها، أو أخفِها من الموقع. الاسم يظهر في النافبار، والوصف يظهر تحت عنوان الصفحة.')
                    ->schema([
                        Repeater::make('website_nav_pages')
                            ->label('الصفحات')
                            ->schema([
                                TextInput::make('href')
                                    ->label('المسار')
                                    ->disabled()
                                    ->dehydrated(),
                                TextInput::make('label_ar')
                                    ->label('اسم الصفحة (عربي)')
                                    ->required()
                                    ->maxLength(120)
                                    ->live(onBlur: true),
                                TextInput::make('label_en')
                                    ->label('اسم الصفحة (إنجليزي)')
                                    ->maxLength(120),
                                Textarea::make('description_ar')
                                    ->label('وصف الصفحة (عربي)')
                                    ->rows(2)
                                    ->helperText('يظهر تحت عنوان الصفحة في الهيرو'),
                                Textarea::make('description_en')
                                    ->label('وصف الصفحة (إنجليزي)')
                                    ->rows(2),
                                Toggle::make('is_visible')
                                    ->label('ظاهرة في الموقع')
                                    ->default(true)
                                    ->live()
                                    ->disabled(fn (Get $get): bool => $get('href') === '/')
                                    ->helperText('إخفاء الصفحة من القائمة ومنع الوصول المباشر إليها'),
                                Toggle::make('show_in_footer')
                                    ->label('إظهار في روابط التذييل')
                                    ->default(true)
                                    ->visible(fn (Get $get): bool => $get('is_visible') !== false),
                            ])
                            ->columns(2)
                            ->reorderable()
                            ->reorderableWithDragAndDrop()
                            ->collapsible()
                            ->collapsed()
                            ->itemNumbers()
                            ->addable(false)
                            ->deletable(false)
                            ->itemLabel(function (array $state): ?string {
                                $label = trim((string) ($state['label_ar'] ?? $state['href'] ?? ''));

                                if ($label === '') {
                                    return null;
                                }

                                $href = trim((string) ($state['href'] ?? ''));
                                $path = $href !== '' && $href !== '/' ? " · {$href}" : '';
                                $hidden = ($state['is_visible'] ?? true) ? '' : ' · مخفية';

                                return "{$label}{$path}{$hidden}";
                            })
                            ->columnSpanFull(),
                    ]),
                Section::make('التذييل')->schema([
                    Textarea::make('footer_text_ar')->label('نص التذييل (عربي)')->rows(2),
                    Textarea::make('footer_text_en')->label('نص التذييل (إنجليزي)')->rows(2),
                ])->columns(2),
                Section::make('الصفحة الرئيسية — الدورات المميزة')
                    ->description('تحكم في ظهور قسم «دورات مميزة» على الصفحة الرئيسية. يمكنك تفعيله دائماً أو خلال فترة زمنية محددة.')
                    ->schema([
                        Toggle::make('homepage_featured_courses_enabled')
                            ->label('إظهار قسم الدورات المميزة')
                            ->default(false)
                            ->live(),
                        DateTimePicker::make('homepage_featured_courses_visible_from')
                            ->label('يظهر من')
                            ->seconds(false)
                            ->nullable()
                            ->visible(fn (callable $get): bool => (bool) $get('homepage_featured_courses_enabled')),
                        DateTimePicker::make('homepage_featured_courses_visible_until')
                            ->label('يظهر حتى')
                            ->seconds(false)
                            ->nullable()
                            ->after('homepage_featured_courses_visible_from')
                            ->visible(fn (callable $get): bool => (bool) $get('homepage_featured_courses_enabled')),
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
        $data['website_nav_pages'] = WebsiteNavPages::normalizeForStorage($data['website_nav_pages'] ?? []);

        foreach ([
            'website_primary_color' => '#002B5B',
            'website_accent_color' => '#C5A04D',
            'website_background_color' => '#f7f9fc',
        ] as $field => $fallback) {
            if (array_key_exists($field, $data)) {
                $data[$field] = HexColor::normalize($data[$field], $fallback);
            }
        }

        $data['website_color_palette'] = WebsiteColorPalettes::resolveStoredPalette($data);
        SiteSetting::current()->update($data);
        AdminPanelSettings::forgetCache();

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
