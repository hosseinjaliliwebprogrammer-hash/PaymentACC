<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class MyDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'My Dashboard';
    protected static ?string $navigationGroup = 'Account';
    protected static ?string $title = 'My Dashboard';
    protected static string $view = 'filament.pages.my-dashboard';

    public static function canView(): bool
    {
        return auth()->check() && ! auth()->user()->is_admin;
    }
}
