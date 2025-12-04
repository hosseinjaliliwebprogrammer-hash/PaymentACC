<?php

namespace App\Filament\Site\Pages;

use Filament\Pages\Page;

class PaymentProcessing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    // نباید در منوی کاربر نمایش داده شود
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.site.pages.payment-processing';
}
