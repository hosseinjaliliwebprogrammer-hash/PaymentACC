<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // نمایش فرم عمومی
    public function create()
    {
        return view('orders.create');
    }

    // ذخیره فرم عمومی
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email',
            'service'     => 'required|string|max:255',
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string',
        ]);

        $data['tracking_code'] = Str::upper(Str::random(10));

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        }

        // درون تراکنش تا همه مراحل یا باهم انجام بشن یا هیچ‌کدوم
        DB::transaction(function () use (&$data) {

            // انتخاب گیت‌وی مناسب بر اساس مبلغ
            $gateway = Gateway::pickForAmount($data['amount']);

            if (! $gateway) {
                abort(400, 'در حال حاضر درگاه فعالی در دسترس نیست.');
            }

            // ساخت سفارش و اتصال گیت‌وی
            $order = Order::create([
                ...$data,
                'status' => 'pending',
                'gateway_id' => $gateway->id,
            ]);

            // افزایش مصرف گیت‌وی
            $gateway->addUsage($order->amount);

            // برای پاس دادن به redirect
            $data['tracking_code'] = $order->tracking_code;
        });

        return redirect()->route('orders.thankyou', ['code' => $data['tracking_code']]);
    }

    // صفحه تشکر
    public function thankyou($code)
    {
        return view('orders.thankyou', compact('code'));
    }

    // لیست سفارش‌های کاربر (فقط برای کاربر لاگین)
    public function myOrders()
    {
        $user = auth()->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $orders = $user->orders()->latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }
}
