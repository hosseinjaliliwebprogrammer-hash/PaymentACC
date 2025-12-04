<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UserDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.user-dashboard';

    protected static ?string $navigationLabel = 'My Dashboard';

    // 👇 دیگه توی گروه "Account" نیست؛ میاد بالا کنار بقیه
    protected static ?string $navigationGroup = null;

    // 👈 عدد منفی یعنی از همه بالاتر
    protected static ?int $navigationSort = -10;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
    }

    public static function canAccess(): bool
    {
        return auth()->check();
    }
}
