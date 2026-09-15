<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\DashboardColorPalettes;
use App\Support\HexColor;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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

class ManageDashboardSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = 'إعدادات لوحة الطالب';

    protected static ?string $title = 'إعدادات لوحة الطالب';

    protected static string|\UnitEnum|null $navigationGroup = 'الإعدادات';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'dashboard-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->toArray());
    }

    public function applyStudentColorPalette(string $paletteId): void
    {
        if ($paletteId === 'custom') {
            $this->data['dashboard_color_palette'] = 'custom';

            return;
        }

        $palette = DashboardColorPalettes::student($paletteId);

        if (! $palette) {
            return;
        }

        $this->data = DashboardColorPalettes::applyStudentPalette(
            array_merge($this->data ?? [], ['dashboard_color_palette' => $paletteId]),
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
                Section::make('الهوية في لوحة الطالب')->schema([
                    TextInput::make('dashboard_institute_name_ar')
                        ->label('اسم المعهد في الشريط الجانبي (عربي)')
                        ->placeholder('معهد العلوم الشرعية'),
                    TextInput::make('dashboard_institute_name_en')
                        ->label('اسم المعهد في الشريط الجانبي (إنجليزي)')
                        ->placeholder('Share3a Institute'),
                    TextInput::make('academic_year_ar')
                        ->label('العام الدراسي (عربي)')
                        ->placeholder('العام الدراسي ١٤٤٦ هـ'),
                    TextInput::make('academic_year_en')
                        ->label('العام الدراسي (إنجليزي)')
                        ->placeholder('Academic Year 1446 AH'),
                    Toggle::make('dashboard_use_site_logo')
                        ->label('استخدام شعار الموقع الرئيسي')
                        ->default(true)
                        ->columnSpanFull(),
                    FileUpload::make('dashboard_logo')
                        ->label('شعار لوحة الطالب')
                        ->image()
                        ->directory('settings/dashboard')
                        ->helperText('يُستخدم عند إيقاف خيار شعار الموقع الرئيسي، أو كبديل عند عدم وجود شعار'),
                ])->columns(2),
                Section::make('الألوان والمظهر')->schema([
                    View::make('filament.forms.color-palette-picker')
                        ->viewData(fn (Get $get): array => [
                            'palettes' => DashboardColorPalettes::studentList(),
                            'selected' => $get('dashboard_color_palette') ?? 'custom',
                            'applyMethod' => 'applyStudentColorPalette',
                            'heading' => 'لوحات ألوان لوحة الطالب',
                            'description' => '١٠ لوحات عصرية — انقر على أي بطاقة لمعاينة الألوان وتطبيقها فوراً على اللون الأساسي، الشريط الجانبي، التمييز، والخلفية.',
                        ])
                        ->columnSpanFull(),
                    ColorPicker::make('dashboard_primary_color')
                        ->label('اللون الأساسي')
                        ->default('#004d40')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('dashboard_color_palette', 'custom')),
                    ColorPicker::make('dashboard_sidebar_color')
                        ->label('لون الشريط الجانبي')
                        ->default('#0a3d34')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('dashboard_color_palette', 'custom')),
                    ColorPicker::make('dashboard_accent_color')
                        ->label('لون التمييز (ذهبي)')
                        ->default('#c9a227')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('dashboard_color_palette', 'custom')),
                    ColorPicker::make('dashboard_background_color')
                        ->label('لون خلفية المحتوى')
                        ->default('#f4f7f6')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set) => $set('dashboard_color_palette', 'custom')),
                    Select::make('dashboard_style')
                        ->label('نمط لوحة التحكم')
                        ->options([
                            'classic' => 'كلاسيكي — مظهر تقليدي أنيق',
                            'modern' => 'عصري — ظلال أقوى وحواف أوضح',
                            'minimal' => 'بسيط — مسطح بدون زخارف',
                            'compact' => 'مضغوط — مساحات أقل للمحتوى الكثيف',
                        ])
                        ->default('classic')
                        ->native(false),
                    Select::make('dashboard_sidebar_style')
                        ->label('نمط الشريط الجانبي')
                        ->options([
                            'dark' => 'داكن',
                            'light' => 'فاتح',
                        ])
                        ->default('dark')
                        ->native(false),
                    Toggle::make('dashboard_show_pattern')
                        ->label('إظهار الزخرفة الإسلامية')
                        ->default(true),
                    Toggle::make('dashboard_compact_mode')
                        ->label('الوضع المضغوط (تقليل الهوامش)')
                        ->default(false),
                ])->columns(2),
                Section::make('تخطيط الصفحة')->schema([
                    Select::make('dashboard_layout')
                        ->label('عرض المحتوى')
                        ->options([
                            'wide' => 'عرض كامل (Wide) — يمتد على كامل الشاشة',
                            'container' => 'حاوية (Container) — محتوى بعرض محدود في الوسط',
                        ])
                        ->default('wide')
                        ->native(false)
                        ->columnSpanFull(),
                ]),
                Section::make('رسالة الترحيب')->schema([
                    Textarea::make('dashboard_welcome_ar')
                        ->label('رسالة الترحيب (عربي)')
                        ->rows(3),
                    Textarea::make('dashboard_welcome_en')
                        ->label('رسالة الترحيب (إنجليزي)')
                        ->rows(3),
                ])->columns(2),
                Section::make('إظهار أقسام لوحة الطالب')->schema([
                    Toggle::make('enable_live_lessons')->label('الدروس المباشرة')->default(true),
                    Toggle::make('enable_hifz')->label('متابعة الحفظ')->default(true),
                    Toggle::make('enable_honor_board')->label('لوحة الشرف')->default(true),
                    Toggle::make('enable_forum')->label('المنتدى الدراسي')->default(true),
                    Toggle::make('enable_wallet')->label('المحفظة')->default(true),
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
            'dashboard_primary_color' => '#004d40',
            'dashboard_sidebar_color' => '#0a3d34',
            'dashboard_accent_color' => '#c9a227',
            'dashboard_background_color' => '#f4f7f6',
        ] as $field => $fallback) {
            if (array_key_exists($field, $data)) {
                $data[$field] = HexColor::normalize($data[$field], $fallback);
            }
        }

        if (empty($data['dashboard_color_palette'])) {
            $data['dashboard_color_palette'] = 'custom';
        }

        SiteSetting::current()->update($data);

        Notification::make()
            ->title('تم حفظ إعدادات لوحة الطالب')
            ->success()
            ->send();
    }
}
