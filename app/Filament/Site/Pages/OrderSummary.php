<?php

namespace App\Filament\Site\Pages;

use App\Models\Order;
use Filament\Pages\Page;

class OrderSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string $view = 'filament.site.pages.order-summary';
    protected static bool $shouldRegisterNavigation = false; // توی منو نشون نده

    // ✅ روش A: اسلاگ با پارامتر
    protected static ?string $slug = 'orders/{tracking_code}/summary';

    public ?Order $order = null;
    public array $publicInstructions = [];

    public function mount(string $tracking_code): void
    {
        $this->order = Order::query()
            ->where('tracking_code', $tracking_code)
            ->firstOrFail();

        // فقط مالک سفارش یا ادمین
        $user = auth()->user();
        if (!$user || (!$user->is_admin && $this->order->user_id !== $user->id)) {
            abort(403);
        }

        // نسخهٔ امن دستور پرداخت از اکسسور مدل
        $this->publicInstructions = $this->order->public_payment_instructions;
    }

    public function getTitle(): string
    {
        return __('Order Summary');
    }
    public static function canAccess(): bool
{
    return auth()->check();
}
}
