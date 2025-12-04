<?php

namespace App\Filament\Widgets;

use App\Models\Gateway;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GatewaysOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s'; // آپدیت خودکار هر ۳۰ ثانیه (اختیاری)

    protected function getStats(): array
    {
        // محاسبه مجموع کل محدودیت‌ها و مصرف‌ها
        $totals = Gateway::query()
            ->selectRaw('COALESCE(SUM(limit_amount),0) AS total_limit, COALESCE(SUM(used_amount),0) AS total_used')
            ->first();

        $totalLimit = (float) $totals->total_limit;
        $totalUsed  = (float) $totals->total_used;
        $remaining  = max($totalLimit - $totalUsed, 0);

        // گیت‌وی‌های فعال
        $activeCount = Gateway::query()->where('is_active', true)->count();

        // گیت‌وی‌های نامحدود
        $unlimitedCount = Gateway::query()->where('limit_amount', 0)->count();

        // سفارش‌های امروز
        $ordersToday = Order::query()
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $amountToday = (float) Order::query()
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        $money = fn (float $v) => '$' . number_format($v, 2);

        return [
            Stat::make('Total Limit', $totalLimit > 0 ? $money($totalLimit) : '—')
                ->description('Sum of gateway limits (0 = unlimited)')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Total Used', $money($totalUsed))
                ->description('Sum used across gateways')
                ->icon('heroicon-o-arrow-trending-up'),

            Stat::make('Remaining', $totalLimit > 0 ? $money($remaining) : '∞')
                ->description($totalLimit > 0 ? 'Limit - Used' : 'Unlimited total')
                ->icon('heroicon-o-battery-50')
                ->color(
                    $totalLimit > 0 && $remaining <= ($totalLimit * 0.1)
                        ? 'danger'
                        : 'success'
                ),

            Stat::make('Active Gateways', (string) $activeCount)
                ->description('Enabled gateways')
                ->icon('heroicon-o-sparkles'),

            Stat::make('Unlimited Gateways', (string) $unlimitedCount)
                ->description('limit_amount = 0')
                ->icon('heroicon-o-circle-stack'),


            Stat::make('Orders Today', $ordersToday . ' / ' . $money($amountToday))
                ->description('count / sum amount')
                ->icon('heroicon-o-clock'),
        ];
    }

    public static function canView(): bool
{
    return auth()->user()?->is_admin ?? false;
}

}
