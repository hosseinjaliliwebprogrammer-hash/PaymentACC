<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PublicOrderController extends Controller
{
    public function showForm()
    {
        $gateways = Gateway::where('is_active', true)->get(['id', 'name', 'email']);
        return view('order', compact('gateways'));
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email',
            'password'   => 'required|string|min:6',
            'service'    => 'required|string|max:255',
            'amount'     => 'required|numeric|min:1',
            'gateway_id' => 'nullable|exists:gateways,id',
            'terms'      => 'accepted',
        ]);

        // 1️⃣ ثبت‌نام یا لاگین
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_admin' => false,
            ]);
        } else {
            if (!Auth::check()) {
                Auth::attempt([
                    'email'    => $validated['email'],
                    'password' => $validated['password'],
                ]);
            }
        }

        Auth::login($user);

        // 2️⃣ انتخاب درگاه پرداخت
        $gateway = $validated['gateway_id']
            ? Gateway::find($validated['gateway_id'])
            : Gateway::pickForAmount($validated['amount']);

        if (! $gateway) {
            return back()->withErrors(['gateway_id' => 'No active gateway with enough limit.'])->withInput();
        }

        // 3️⃣ ایجاد سفارش
        $order = Order::create([
            'user_id'       => $user->id,
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'service'       => $validated['service'],
            'amount'        => $validated['amount'],
            'gateway_id'    => $gateway->id,
            'description'   => $request->input('description'),
            'status'        => 'pending',
            'tracking_code' => strtoupper(str()->random(10)),
        ]);

        $gateway->addUsage($order->amount);

        // 4️⃣ انتقال به صفحه‌ی موفقیت
        return redirect()->route('order.success')->with('tracking', $order->tracking_code);
    }

    public function success()
    {
        $tracking = session('tracking');
        return view('order-success', compact('tracking'));
    }
}
