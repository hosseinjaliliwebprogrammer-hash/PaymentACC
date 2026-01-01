<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailTemplate;

class PaypalController extends Controller
{


    public function success(Request $request)
    {
        $jwt_secret = config('app.key');

        $token = $request->token ?? null;
        $payload = JWT::decode($token, new Key($jwt_secret, 'HS256'));

        $order = Order::findOrFail($payload->order_id);
        $order->status = 'completed';
        $order->save();
        return redirect()->route('payment.success');
    }

    public function cancel(Request $request)
    {
        $jwt_secret = config('app.key');

        $token = $request->token ?? null;
        $payload = JWT::decode($token, new Key($jwt_secret, 'HS256'));


        $order = Order::where('id',$payload->order_id)->first();
        if ($order && $order->status == 'processing') {
            $order->status = 'cancelled';
            $order->save();
        }

        return redirect()->route('payment.failed');
    }
}
