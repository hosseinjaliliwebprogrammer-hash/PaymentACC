<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\GatewaysOverview;
use App\Filament\Widgets\RecentOrders;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static ?string $title = 'Dashboard';

    /** فقط ادمین آیتم منو را می‌بیند */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->is_admin ?? false;
    }

    /** اگر یوزر معمولی وارد /admin شد، به My Dashboard هدایت شود */
    public function mount(): void
    {
        if (! (auth()->user()?->is_admin ?? false)) {
            // اسم روت صفحهٔ MyDashboard در Filament v3 اینه:
            $this->redirectRoute('filament.admin.pages.my-dashboard', navigate: true);
        }
    }

    /** برای اطمینان: یوزر معمولی هیچ ویجتی از داشبورد ادمین را نمی‌بیند */
    public function getWidgets(): array
    {
        if (! (auth()->user()?->is_admin ?? false)) {
            return []; // هیچ چیزی رندر نشود
        }

        return [
            GatewaysOverview::class,
            RecentOrders::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'sm' => 1,
            'md' => 2,
        ];
    }
}
