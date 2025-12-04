<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NowPaymentsIpnController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| ONLY NOWPayments IPN should be here.
| This route does NOT use CSRF (api middleware only).
|
*/

Route::post('/nowpayments/ipn', [NowPaymentsIpnController::class, 'handle'])
    ->name('nowpayments.ipn');
