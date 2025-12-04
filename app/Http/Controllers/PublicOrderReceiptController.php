<?php

namespace App\Http\Controllers;

use App\Models\Order;

class PublicOrderReceiptController extends Controller
{
    public function show(string $tracking_code)
    {
        $order = Order::where('tracking_code', $tracking_code)->firstOrFail();

        // فقط نسخهٔ امن دستور پرداخت را به ویو می‌فرستیم
        $safe = $order->public_payment_instructions;

        return view('orders.public-receipt', [
            'order' => $order,
            'instructions' => $safe,
        ]);
    }
}
