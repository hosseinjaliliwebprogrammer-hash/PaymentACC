<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\EmailTemplate;

class PaygateReturnController extends Controller
{
    /**
     * چون در routes از ->name('paygate.return') با کنترلر استفاده می‌کنیم،
     * بهتر است از متد index استفاده کنیم (نه __invoke)
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $amount = $request->get('amount');
        $trackingId = $request->get('tracking');



        // ====== PAYMENT SUCCESS ======
        if ($status === 'success' && $trackingId) {
            $order = Order::where('tracking_code', $trackingId)->first();
            if(!$order){
                return redirect()->route('payment.failed');
            }

            $email = $order->email;

            if (! $email) {
                Log::warning('PaygateReturn: No authenticated user, skipping email send.');
                return redirect()->route('payment.success');
            }

            // Template with slug = "payget"
            $template = EmailTemplate::where('slug', 'payget')->first();

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

            return redirect()->route('payment.success');
        }

        // ====== PAYMENT FAILED ======
        return redirect()->route('payment.failed');
    }
}
