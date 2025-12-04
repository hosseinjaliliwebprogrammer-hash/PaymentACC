<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

// 🟢 اضافه: ایمپورت ارسال ایمیل
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmedMail;

class NowPaymentsIpnController extends Controller
{
    /**
     * Handle NOWPayments IPN callback.
     */
    public function handle(Request $request)
    {
        $ipnSecret = config('services.nowpayments.ipn_secret');

        // امضایی که NOWPayments تو هدر می‌فرسته
        $receivedSignature = $request->header('x-nowpayments-sig');

        // بدنه خام درخواست
        $body = $request->getContent();

        // محاسبه امضا با ipn_secret
        $calculatedSignature = hash_hmac('sha512', $body, $ipnSecret);

        if (! hash_equals($calculatedSignature, $receivedSignature)) {
            Log::warning('NOWPayments IPN: Invalid signature', [
                'received'   => $receivedSignature,
                'calculated' => $calculatedSignature,
                'body'       => $body,
            ]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = $request->json()->all();
        Log::info('NOWPayments IPN received', $data);

        $orderId = $data['order_id'] ?? null;          // ما تو createPayment، order_id را = $order->id فرستادیم
        $status  = $data['payment_status'] ?? null;    // waiting / confirming / finished / failed / refunded / expired

        if (! $orderId || ! $status) {
            return response()->json(['error' => 'Invalid IPN payload'], 422);
        }

        $order = Order::find($orderId);

        if (! $order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // مپ وضعیت‌ها به وضعیت سفارش خودت
        if ($status === 'finished') {
            $order->status = 'paid';
        } elseif (in_array($status, ['failed', 'expired', 'refunded'])) {
            $order->status = 'failed';
        } else {
            // waiting / confirming / …
            $order->status = 'pending';
        }

        // می‌تونی وضعیت خام NOWPayments را هم ذخیره کنی
        $instructions = $order->payment_instructions ?? [];
        if (is_array($instructions)) {
            $instructions['nowpay_raw_status'] = $status;
            $order->payment_instructions = $instructions;
        }

        $order->save();

        // 🟢 ارسال ایمیل بعد از موفقیت پرداخت (بدون خراب کردن کد)
        if ($status === 'finished') {
            try {
                Mail::to($order->email)->send(new PaymentConfirmedMail($order));
            } catch (\Throwable $e) {
                Log::error('NOWPayments Email Error', [
                    'order_id' => $order->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }
}
