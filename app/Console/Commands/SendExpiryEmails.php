<?php

namespace App\Console\Commands;

use App\Models\EmailTemplate;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendExpiryEmails extends Command
{
    protected $signature = 'send:expiry-emails';
    protected $description = 'Send expiry notifications to customers whose orders are close to expiration';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentDate = Carbon::now();

        Log::info('Exit sendExpiryEmails');

        $orders = Order::where(function ($query) use ($currentDate) {
            $query->whereDate('expire_at', $currentDate->copy()->addDay(1))  // یک روز مانده
            ->orWhereDate('expire_at', $currentDate->copy()->addDays(4))  // چهار روز مانده
            ->orWhereDate('expire_at', $currentDate->copy()->addDays(7));  // یک هفته مانده
        })->where(function ($query) use ($currentDate) {
            $query->where('send_at','<', $currentDate->copy()->subDay(1))
                ->orWhereNull('send_at');
        })
        ->get();



        foreach ($orders as $order) {
            Log::info('Exit order: ' . $order->id );
            $order->send_at = Carbon::now();
            $order->save();
            try {
                $orderExpireDate = Carbon::parse($order->expire_at); // تاریخ انقضا سفارش
                $remainingDays = Carbon::now()->diffInDays($orderExpireDate, false);
                $allowedValues = [1, 4, 7];

                $remainingDay = $allowedValues[0];
                $minDifference = abs($remainingDays - $allowedValues[0]);

                foreach ($allowedValues as $value) {
                    $difference = abs($remainingDays - $value); // محاسبه تفاوت
                    if ($difference < $minDifference) {
                        $minDifference = $difference;
                        $remainingDay = $value; // به روز رسانی نزدیک‌ترین مقدار
                    }
                }


                $email = $order->email;
                $template = EmailTemplate::where('slug', 'reminder_'.$remainingDay.'_day_before')->first();

                Log::info('Email sent to customer with Reminder Day: ' . $remainingDay);

                if ($template) {
                    $body = str_replace(
                        ['{name}', '{service}', '{amount}', '{tracking_code}', '{order_id}'],
                        [$order->name, $order->service, $order->amount, $order->tracking_code,$order->id],
                        $template->body ?? ''
                    );

                    Mail::html($body, function ($msg) use ($email, $template) {
                        $msg->to($email)
                            ->subject($template->subject)
                            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                    });

                    Log::info('Email sent to customer with order ID: ' . $order->id);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send email to customer with order ID: ' . $order->id . '. Error: ' . $e->getMessage());
            }
        }


    }
}
