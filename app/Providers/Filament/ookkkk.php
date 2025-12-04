<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;

// صفحات پنل سایت
use App\Filament\Pages\UserDashboard;          // مسیر قدیمی (بدون Site\)
use App\Filament\Site\Pages\OrderSummary;      // خلاصه سفارش
use App\Filament\Site\Pages\OrderForm;         // فرم سفارش (می‌خواهیم public باشد)

class SitePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('site')
            ->path('app')
            ->homeUrl(fn () => route('filament.site.pages.user-dashboard'))
            ->colors([
                'primary' => Color::Blue,
            ])

            // احراز هویت و ثبت‌نام (فعال می‌ماند، ولی پنل را بدون auth می‌چرخانیم)
            ->login()
            ->registration()
            ->passwordReset()
            // ->emailVerification()

            // فقط منابع مخصوص کاربران (مانند سفارش‌ها)
            ->resources([
                \App\Filament\Resources\OrderResource::class,
            ])

            // صفحات پنل سایت
            ->pages([
                UserDashboard::class,   // فقط برای کاربران لاگین (داخل خود صفحه محدود می‌کنیم)
                OrderSummary::class,    // فقط برای کاربران لاگین (داخل خود صفحه محدود می‌کنیم)
                OrderForm::class,       // برای مهمان‌ها هم باز است
            ])

            // میان‌افزارها
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

            // توجه: پنل را بدون auth اجرا می‌کنیم؛ کنترل دسترسی در خود صفحات انجام می‌شود
            ->authMiddleware([]);
    }
}
