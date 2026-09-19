<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ManageAdminSettings;
use App\Filament\Pages\ManageDashboardSettings;
use App\Filament\Pages\ManageRegulations;
use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Pages\ManageStudyPlan;
use App\Filament\Widgets\LatestContactMessages;
use App\Filament\Widgets\LatestEnrollments;
use App\Filament\Widgets\LatestStudentRegistrations;
use App\Filament\Widgets\StatsOverview;
use App\Support\AdminPanelSettings;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->favicon(asset('favicon.svg'))
            ->brandName(fn () => AdminPanelSettings::brandName())
            ->brandLogo(fn () => AdminPanelSettings::brandLogoUrl())
            ->brandLogoHeight(fn () => AdminPanelSettings::brandLogoHeight())
            ->colors(fn () => [
                'primary' => AdminPanelSettings::primaryColor(),
            ])
            ->maxContentWidth(AdminPanelSettings::maxContentWidth())
            ->topNavigation(fn () => AdminPanelSettings::usesTopNavigation())
            ->sidebarCollapsibleOnDesktop(fn () => AdminPanelSettings::sidebarCollapsible())
            ->sidebarWidth(AdminPanelSettings::sidebarWidth())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->navigationGroups([
                'الهيكل الأكاديمي',
                'المحتوى التعليمي',
                'الدورات',
                'الإدارة الأكاديمية',
                'المحتوى',
                'التواصل',
                'النظام',
            ])
            ->pages([
                Dashboard::class,
                ManageSiteSettings::class,
                ManageStudyPlan::class,
                ManageRegulations::class,
                ManageDashboardSettings::class,
                ManageAdminSettings::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverview::class,
                LatestStudentRegistrations::class,
                LatestEnrollments::class,
                LatestContactMessages::class,
                AccountWidget::class,
            ])
            ->renderHook(
                PanelsRenderHook::SCRIPTS_BEFORE,
                fn (): string => '<script src="'.e(asset('js/tus.min.js')).'"></script><script src="'.e(asset('js/bunny-lesson-uploader.js')).'"></script>',
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('@include(\'filament.admin-custom-styles\')'),
            );
    }
}
