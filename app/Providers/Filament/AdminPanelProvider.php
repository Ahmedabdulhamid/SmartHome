<?php

namespace App\Providers\Filament;

use App\Filament\Pages\SalesReports;
use App\Filament\Resources\DashboardResource\Widgets\AdminChart;
use App\Filament\Resources\DashboardResource\Widgets\AdminStatsOverview;
use App\Filament\Resources\DashboardResource\Widgets\ProductChart;
use App\Filament\Resources\DashboardResource\Widgets\UserAdminChart;
use App\Http\Middleware\RedirectIfAuthent;
use App\Models\Admin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\UserMenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color; // تأكد من استيراد Color
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;
use App\Http\Middleware\RedirectLivewire;
use App\Http\Middleware\SetLocale;
use App\Models\Setting;
use Filament\Navigation\NavigationGroup;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $settings = Cache::remember(
            'filament.admin.panel.settings',
            now()->addHours(6),
            fn () => Setting::query()->first()
        );

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('admin')

            ->favicon($settings?->favicon ? asset('storage/' . $settings->favicon) : null)
            ->colors([

                'primary' => Color::Violet,
            ])

            // 💡 التعديل: تفعيل خاصية الوضع الليلي
            ->darkMode(false) // يسمح للمستخدم بتبديل الوضع الليلي (Dark/Light)

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                SalesReports::class
            ])

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                AdminChart::class,
                ProductChart::class,
                UserAdminChart::class,
                AdminStatsOverview::class,


            ])
            ->middleware([

                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                RedirectIfAuthent::class

            ])->middleware(
                [
                    SetLocale::class,
                ]
            )->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn(): string => Blade::render("@livewire('language-toggle')")
            )
            ->databaseNotifications()



            ->authMiddleware([
                Authenticate::class,

            ])
            ->darkMode(true)

        ;
    }

}
