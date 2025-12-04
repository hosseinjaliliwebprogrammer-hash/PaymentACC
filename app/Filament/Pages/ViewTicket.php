<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ViewTicket extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // ❌ این خط باعث می‌شود این صفحه در سایدبار ادمین نمایش داده نشود
    protected static bool $shouldRegisterNavigation = false;

    // لازم نیست این ویو وجود داشته باشد، فقط override لازم داریم
    protected static string $view = 'filament.pages.view-ticket';
}
