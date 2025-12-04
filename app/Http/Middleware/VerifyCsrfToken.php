<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // NOWPayments IPN (هر حالتی از اسلش و ساب‌مسیرها)
        'nowpayments/ipn',
        'nowpayments/ipn/',
        'nowpayments/ipn/*',
        '/nowpayments/ipn',
        '/nowpayments/ipn/',
        '/nowpayments/ipn/*',
    ];
}
