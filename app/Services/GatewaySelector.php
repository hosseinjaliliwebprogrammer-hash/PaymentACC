<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Gateway;

class GatewaySelector
{
    public static function getAvailableGateway()
    {
        return Gateway::where('is_active', 1)
            ->whereColumn('used_amount', '<', 'limit_amount')
            ->orderBy('id', 'ASC') // پر کن از پایین‌ترین ID به بالا
            ->first();
    }

    public static function assignToOrder($order)
    {
        $gateway = self::getAvailableGateway();

        if (!$gateway) {
            throw new \Exception('No available PayPal gateway found');
        }

        // لینک Gateway به سفارش
        $order->gateway_id = $gateway->id;
        $order->save();

        // آپدیت used_amount
        $orderAmount = $order->amount ?? 0;
        $gateway->used_amount += $orderAmount;
        $gateway->save();

        // 🟡 اضافه شد — متن ایمیل مخصوص این Gateway
        $emailTemplate = self::generateEmailTemplate($gateway, $order);

        return [
            'gateway' => $gateway,
            'email_template' => $emailTemplate,
        ];
    }

    /**
     * تولید متن ایمیل بر اساس نوع Template
     */
    public static function generateEmailTemplate(Gateway $gateway, $order)
    {
        $templateType = strtolower($gateway->email_template_type ?? 'custom');

        switch ($templateType) {
            case 'send':
                return <<<EOT
Dear {$order->name},

Please complete your payment using PayPal:

PayPal Email: {$gateway->email}
Payment URL: {$gateway->link}
Amount: \${$order->amount}
Service: {$order->product->name}

After payment, please click the link below to activate your account:
{$order->tracking_url}

Thank you,
The PaymentACC Team
EOT;

            case 'standard':
                return "Hi {$order->name}, your order of {$order->product->name} for \${$order->amount} has been received.";

            case 'custom':
            default:
                return $gateway->email_template_body
                    ?? "Hi {$order->name}, pay \${$order->amount} for {$order->product->name}. Tracking: {$order->tracking_code}.";
        }
    }
}
