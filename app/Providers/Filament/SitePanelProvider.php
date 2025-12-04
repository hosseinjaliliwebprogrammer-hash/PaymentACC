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

use Filament\Navigation\MenuItem;
use App\Filament\Site\Pages\AccountSettings;

// صفحات کاربر
use App\Filament\Pages\UserDashboard;
use App\Filament\Site\Pages\OrderSummary;
use App\Filament\Site\Pages\OrderForm;
use App\Filament\Site\Pages\MyTickets;
use App\Filament\Site\Pages\CreateTicket;
use App\Filament\Site\Pages\ViewTicket;

class SitePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('site')
            ->path('app')

            // 👇 بعد از لاگین و برای لینک Home، یوزر میره User Dashboard
            ->homeUrl(fn () => UserDashboard::getUrl(panel: 'site'))

            ->colors([
                'primary' => Color::Blue,
            ])
            ->login()
            ->registration()
            ->passwordReset()
            ->resources([
                \App\Filament\Resources\OrderResource::class,
            ])
            ->pages([
                UserDashboard::class,
                OrderSummary::class,
                OrderForm::class,
                MyTickets::class,
                CreateTicket::class,
                ViewTicket::class,
                AccountSettings::class,
            ])
            ->userMenuItems([
                'account-settings' => MenuItem::make()
                    ->label('Account Settings')
                    ->url(fn (): string => AccountSettings::getUrl(panel: 'site'))
                    ->icon('heroicon-o-user-circle'),
            ])
            ->discoverPages(
                in: app_path('Filament/Site/Pages'),
                for: 'App\\Filament\\Site\\Pages',
            )
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
            ->authMiddleware([])
            ->darkMode(false);
    }
}
