<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('newOrder')
                ->label('New order')            // متن همان دکمه قبلی
                ->icon('heroicon-o-plus')       // آیکن New order
                ->url('/app/order')             // لینک سفارشی تو
                ->color('primary'),             // همان رنگ پیش‌فرض
        ];
    }
}
