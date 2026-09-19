<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Subjects\Tables\StudyPlanAssignmentsTable;
use App\Models\CurriculumAssignment;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageStudyPlan extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'الخطة الدراسية / المقررات الدراسية';

    protected static ?string $title = 'إدارة الخطة الدراسية والمقررات';

    protected static string|\UnitEnum|null $navigationGroup = 'الهيكل الأكاديمي';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'study-plan';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSetting::current();

        $this->form->fill([
            'study_plan_intro_ar' => $settings->study_plan_intro_ar,
            'study_plan_intro_en' => $settings->study_plan_intro_en,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('معاينة في الموقع')
                ->icon(Heroicon::OutlinedEye)
                ->url(rtrim(config('app.frontend_url'), '/').'/study-plan/')
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
                Section::make('مقدمة الخطة الدراسية')
                    ->description('النص الذي يظهر أعلى صفحة الخطة الدراسية في الموقع')
                    ->schema([
                        Textarea::make('study_plan_intro_ar')
                            ->label('المقدمة (عربي)')
                            ->rows(4)
                            ->columnSpanFull(),
                        Textarea::make('study_plan_intro_en')
                            ->label('المقدمة (إنجليزي)')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return StudyPlanAssignmentsTable::configure($table)
            ->query(
                CurriculumAssignment::query()
                    ->with(['semester.year.level', 'specialization', 'subject.course'])
            )
            ->description('عدّل الحفظ واسم الكتاب والكتب التكميلية لكل مادة');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    EmbeddedSchema::make('form'),
                ])
                    ->id('study-plan-intro-form')
                    ->livewireSubmitHandler('saveIntro')
                    ->footer([
                        Actions::make([
                            Action::make('saveIntro')
                                ->label('حفظ المقدمة')
                                ->submit('saveIntro'),
                        ]),
                    ]),
                Section::make('جدول المواد الدراسية')
                    ->schema([
                        EmbeddedTable::make(),
                    ]),
            ]);
    }

    public function saveIntro(): void
    {
        $data = $this->form->getState();

        SiteSetting::current()->update([
            'study_plan_intro_ar' => $data['study_plan_intro_ar'] ?? null,
            'study_plan_intro_en' => $data['study_plan_intro_en'] ?? null,
        ]);

        Notification::make()
            ->title('تم حفظ مقدمة الخطة الدراسية')
            ->success()
            ->send();
    }
}
