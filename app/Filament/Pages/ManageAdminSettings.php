<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\AdminPanelSettings;
use App\Support\DashboardColorPalettes;
use App\Support\HexColor;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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

class ManageAdminSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $navigationLabel = 'إعدادات لوحة الإدارة';

    protected static ?string $title = 'إعدادات لوحة الإدارة';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'admin-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->toArray());
    }

    public function applyAdminColorPalette(string $paletteId): void
    {
        if ($paletteId === 'custom') {
            $this->data['admin_color_palette'] = 'custom';

            return;
        }

        $palette = DashboardColorPalettes::admin($paletteId);

        if (! $palette) {
            return;
        }

        $this->data = DashboardColorPalettes::applyAdminPalette(
            array_merge($this->data ?? [], ['admin_color_palette' => $paletteId]),
            $paletteId,
        );
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('الهوية في لوحة الإدارة')->schema([
                    TextInput::make('admin_brand_name_ar')
                        ->label('اسم لوحة الإدارة (عربي)')
                        ->placeholder('معهد علم شرعي'),
                    TextInput::make('admin_brand_name_en')
                        ->label('اسم لوحة الإدارة (إنجليزي)')
                        ->placeholder('Share3a Admin'),
                    Toggle::make('admin_use_site_logo')
                        ->label('استخدام شعار الموقع الرئيسي')
                        ->default(true)
                        ->columnSpanFull(),
                    FileUpload::make('admin_logo')
                        ->label('شعار لوحة الإدارة')
                        ->image()
                        ->directory('settings/admin')
                        ->helperText('يظهر في الشريط الجانبي وشاشة تسجيل الدخول'),
                ])->columns(2),
                Section::make('الألوان والمظهر')->schema([
                    View::make('filament.forms.color-palette-picker')
                        ->viewData(fn (Get $get): array => [
                            'palettes' => DashboardColorPalettes::adminList(),
                            'selected' => $get('admin_color_palette') ?? 'custom',
                            'applyMethod' => 'applyAdminColorPalette',
                            'heading' => 'لوحات ألوان لوحة الإدارة',
                            'description' => '١٠ لوحات عصرية — انقر على أي بطاقة لمعاينة الألوان وتطبيقها فوراً على لوحة Filament.',
                        ])
                        ->columnSpanFull(),
                    ColorPicker::make('admin_primary_color')
                        ->label('اللون الأساسي')
                        ->default('#059669')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('admin_color_palette', 'custom')),
                    ColorPicker::make('admin_sidebar_color')
                        ->label('لون الشريط الجانبي')
                        ->default('#0f172a')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('admin_color_palette', 'custom')),
                    ColorPicker::make('admin_accent_color')
                        ->label('لون التمييز')
                        ->default('#c9a227')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('admin_color_palette', 'custom')),
                    ColorPicker::make('admin_background_color')
                        ->label('لون خلفية المحتوى')
                        ->default('#f8fafc')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('admin_color_palette', 'custom')),
                    Select::make('admin_style')
                        ->label('نمط لوحة الإدارة')
                        ->options([
                            'classic' => 'كلاسيكي — مظهر Filament التقليدي',
                            'modern' => 'عصري — ظلال أوضح وحواف بارزة',
                            'minimal' => 'بسيط — مسطح بدون زخارف',
                            'compact' => 'مضغوط — مساحات أقل',
                        ])
                        ->default('classic')
                        ->native(false),
                    Select::make('admin_sidebar_style')
                        ->label('نمط الشريط الجانبي')
                        ->options([
                            'dark' => 'داكن',
                            'light' => 'فاتح',
                        ])
                        ->default('dark')
                        ->native(false),
                    Toggle::make('admin_show_pattern')
                        ->label('إظهار الزخرفة الإسلامية')
                        ->default(false),
                    Toggle::make('admin_compact_mode')
                        ->label('الوضع المضغوط')
                        ->default(false),
                ])->columns(2),
                Section::make('تخطيط الصفحة والتنقل')->schema([
                    Select::make('admin_layout')
                        ->label('عرض المحتوى')
                        ->options([
                            'wide' => 'عرض كامل (Wide)',
                            'container' => 'حاوية (Container) — عرض محدود',
                            'full' => 'ملء الشاشة (Full)',
                        ])
                        ->default('wide')
                        ->native(false),
                    Select::make('admin_navigation')
                        ->label('نوع التنقل')
                        ->options([
                            'sidebar' => 'شريط جانبي (Sidebar)',
                            'top' => 'شريط علوي (Top Navigation)',
                        ])
                        ->default('sidebar')
                        ->native(false),
                    Toggle::make('admin_sidebar_collapsible')
                        ->label('السماح بطي الشريط الجانبي')
                        ->default(true)
                        ->columnSpanFull(),
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

        foreach ([
            'admin_primary_color' => '#059669',
            'admin_sidebar_color' => '#0f172a',
            'admin_accent_color' => '#c9a227',
            'admin_background_color' => '#f8fafc',
        ] as $field => $fallback) {
            if (array_key_exists($field, $data)) {
                $data[$field] = HexColor::normalize($data[$field], $fallback);
            }
        }

        if (empty($data['admin_color_palette'])) {
            $data['admin_color_palette'] = 'custom';
        }

        SiteSetting::current()->update($data);
        AdminPanelSettings::forgetCache();

        Notification::make()
            ->title('تم حفظ إعدادات لوحة الإدارة')
            ->body('جاري تحديث الصفحة لتطبيق الألوان الجديدة...')
            ->success()
            ->send();

        $this->redirect(static::getUrl(), navigate: false);
    }
}
