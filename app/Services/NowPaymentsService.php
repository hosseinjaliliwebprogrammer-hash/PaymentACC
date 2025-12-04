<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class NowPaymentsService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.nowpayments.base_url', 'https://api.nowpayments.io/v1');
        $this->apiKey  = config('services.nowpayments.api_key');
    }

    /**
     * ایجاد پرداخت مستقیم /v1/payment
     */
    public function createPayment(
        Order $order,
        string $payCurrency = 'usdttrc20',
        ?string $successUrl = null,
        ?string $cancelUrl = null
    ): array {

        $payload = [
            'price_amount'      => (float) $order->amount,
            'price_currency'    => 'usd',
            'pay_currency'      => $payCurrency,
            'order_id'          => (string) $order->id,
            'order_description' => $order->service ?? ($order->product->name ?? 'Order #'.$order->id),

            // ✔️ فقط این درست است
            'ipn_callback_url'  => route('nowpayments.ipn'),
        ];

        // success URL
        $payload['success_url'] =
            $successUrl ?: url('/payment/success');

        // cancel URL
        $payload['cancel_url'] =
            $cancelUrl ?: url('/payment/failed');

        $response = Http::withHeaders([
            'x-api-key'    => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/payment', $payload);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'NOWPayments API error (payment): ' . $response->status() . ' - ' . $response->body()
            );
        }

        return $response->json();
    }

    /**
     * ایجاد اینویس /v1/invoice
     */
    public function createInvoice(
        Order $order,
        ?string $successUrl = null,
        ?string $cancelUrl = null
    ): array {

        $payload = [
            'price_amount'      => (float) $order->amount,
            'price_currency'    => 'usd',
            'order_id'          => (string) $order->id,
            'order_description' => $order->service ?? ($order->product->name ?? 'Order #'.$order->id),

            // ✔️ درست‌ترین حالت ممکن
            'ipn_callback_url'  => route('nowpayments.ipn'),
        ];

        $payload['success_url'] =
            $successUrl ?: url('/payment/success');

        $payload['cancel_url'] =
            $cancelUrl ?: url('/payment/failed');

        $response = Http::withHeaders([
            'x-api-key'    => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/invoice', $payload);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'NOWPayments API error (invoice): ' . $response->status() . ' - ' . $response->body()
            );
        }

        return $response->json();
    }
}
