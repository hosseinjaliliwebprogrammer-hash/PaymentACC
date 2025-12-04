<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

// 🟢 اضافه: ایمپورت ارسال ایمیل
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmedMail;

class PaygateWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // ما در callback، tracking_code را QueryString می‌فرستیم
        $tracking = $request->query('tracking');

        if (! $tracking) {
            Log::warning('Paygate webhook without tracking', [
                'ip'  => $request->ip(),
                'all' => $request->all(),
            ]);

            return response('missing tracking', 400);
        }

        $order = Order::where('tracking_code', $tracking)->first();

        if (! $order) {
            Log::warning('Paygate webhook order not found', [
                'tracking' => $tracking,
            ]);

            return response('order not found', 404);
        }

        // ذخیره‌ی کامل payload داخل payment_instructions (برای لاگ و دیباگ)
        $pi = (array) ($order->payment_instructions ?? []);
        $pi['paygate_webhook_payload'] = $request->all();
        $order->payment_instructions = $pi;

        // ساده‌ترین منطق: وقتی وبهوک خورد → سفارش را paid علامت بزن
        $wasPaidBefore = ($order->status === 'paid');

        if (! $wasPaidBefore) {
            $order->status = 'paid';
        }

        $order->save();

        // 🟢 ارسال ایمیل فقط اگر تازه paid شده باشد (دوبار ارسال نشود)
        if (! $wasPaidBefore) {
            try {
                Mail::to($order->email)->send(new PaymentConfirmedMail($order));
            } catch (\Throwable $e) {
                Log::error('Paygate Email Error', [
                    'tracking' => $tracking,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        Log::info('Paygate webhook processed', [
            'tracking' => $tracking,
        ]);

        return response('ok', 200);
    }
}
