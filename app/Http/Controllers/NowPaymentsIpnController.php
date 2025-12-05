<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\NowPaymentsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailTemplate;

class NowPaymentsIpnController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Incoming Request:', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'params' => $request->all(),
        ]);

        $signature = $request->header('x-nowpayments-sig');
        $data = $request->all();

        $now = new NowPaymentsService();
        if ($now->verifySignature($data, $signature)) {

            if ($data['status'] == 'confirmed') {
                $order = Order::query()->find($data['order_id']);
                $this->sendEmail($order);

            }
        }

        return response()->json(['status' => 'success']);
    }



    private function sendEmail($order)
    {
        $template = EmailTemplate::where('slug', 'nowpay')->first();
        $email = $order->email;

        if ($template) {

            // Replace template variables
            $body = str_replace(
                ['{name}', '{service}', '{amount}', '{tracking_code}'],
                [$order->name, $order->service, $order->amount, $order->tracking_code],
                $template->body ?? ''
            );
            // Send confirmation email
            try {
                $result = Mail::html($body, function ($msg) use ($email, $template) {
                    $msg->to($email)
                        ->subject($template->subject ?? 'Your Paygate Payment Confirmation')
                        ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                });
            } catch (\Throwable $e) {
                // Do NOT break the redirect if email fails
                Log::warning('PaygateReturn: email send failed', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
