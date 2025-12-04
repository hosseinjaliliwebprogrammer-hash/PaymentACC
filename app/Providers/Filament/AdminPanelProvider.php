<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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
            ->colors([
                'primary' => Color::Amber,
            ])

            // 🏠 خانهٔ پنل: همیشه داشبورد ادمین
            ->homeUrl(function () {
                return route('filament.admin.pages.dashboard');
            })

            /*
            |--------------------------------------------------------------------------
            | 📦 منابع (Resources)
            |--------------------------------------------------------------------------
            | هم منابع تعریف‌شدهٔ خاص و هم کشف خودکار کل Resources پوشهٔ Filament
            |--------------------------------------------------------------------------
            */
            ->resources([
                \App\Filament\Resources\OrderResource::class,
                \App\Filament\Resources\GatewayResource::class,
                \App\Filament\Resources\ProductResource::class,
                // 👇 TicketResource به‌صورت مستقیم اضافه شد برای اطمینان
                \App\Filament\Resources\TicketResource::class,
            ])
            // 👇 کشف خودکار بقیهٔ منابع (مثل TicketResource یا هر منبع جدید)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')

            /*
            |--------------------------------------------------------------------------
            | 📄 صفحات (Pages)
            |--------------------------------------------------------------------------
            */
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')

            /*
            |--------------------------------------------------------------------------
            | 📊 ویجت‌ها (Widgets)
            |--------------------------------------------------------------------------
            */
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])

            /*
            |--------------------------------------------------------------------------
            | ⚙️ میان‌افزارها (Middlewares)
            |--------------------------------------------------------------------------
            */
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
            ])

            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
