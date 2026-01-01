<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicOrderReceiptController;
use App\Http\Controllers\PaygateReturnController;
use App\Http\Controllers\PaygateWebhookController;
use App\Http\Controllers\NowPaymentsIpnController;
use App\Http\Controllers\PaypalController;
use App\Models\User;
use App\Filament\Site\Pages\PaymentProcessing;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Public-facing website routes.
| Filament panels (admin + site) automatically register their own routes.
|
*/

/* 🔁 Redirect Homepage → /app/login */
Route::redirect('/', '/app/login');

// 🧭 Breeze dashboard
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// 👤 User Profile (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 🔁 Redirect → Filament Site Order Page
|--------------------------------------------------------------------------
*/
Route::redirect('/order', '/app/order')->name('order.short');

/*
|--------------------------------------------------------------------------
| 📄 Public Order Receipt (signed only)
|--------------------------------------------------------------------------
*/
Route::get('/orders/{tracking_code}/receipt', [PublicOrderReceiptController::class, 'show'])
    ->name('orders.receipt')
    ->middleware('signed');

/*
|--------------------------------------------------------------------------
| 💳 Paygate.to Payment Routes
|--------------------------------------------------------------------------
*/
Route::any('/paygate/webhook', [PaygateWebhookController::class, 'handle'])
    ->name('paygate.webhook');

Route::get('/paygate/return', [PaygateReturnController::class, 'index'])
    ->name('paygate.return');




/*
|--------------------------------------------------------------------------
| 💳 card2crypto.org Payment Routes
|--------------------------------------------------------------------------
*/
Route::any('/card2crypto/webhook', [Card2cryptoWebhookController::class, 'handle'])
    ->name('card2crypto.webhook');

Route::get('/card2crypto/return', [Card2cryptoReturnController::class, 'index'])
    ->name('card2crypto.return');


/*
|--------------------------------------------------------------------------
| 💳 Payment Result Pages
|--------------------------------------------------------------------------
*/

Route::any('/paypal/success', [PaypalController::class, 'success'])
    ->name('paypal.success');
Route::any('/paypal/failed', [PaypalController::class, 'cancel'])
    ->name('paypal.failed');


Route::get('/payment/success', fn () => view('payment.success'))
    ->name('payment.success');

Route::get('/payment/failed', fn () => view('payment.failed'))
    ->name('payment.failed');

/*
|--------------------------------------------------------------------------
| 🪙 NOWPayments — IPN Webhook
|--------------------------------------------------------------------------
*/
Route::post('/nowpayments/ipn', [NowPaymentsIpnController::class, 'handle'])
    ->name('nowpayments.ipn');

/*
|--------------------------------------------------------------------------
| 🧾 PayPal Manual Payment — Processing Page
|--------------------------------------------------------------------------
*/
Route::get('/payment-processing', PaymentProcessing::class)
    ->name('filament.site.pages.payment-processing');

/*
|--------------------------------------------------------------------------
| 🧩 Breeze Registration + Filament Site Sync
|--------------------------------------------------------------------------
*/
Route::post('/register', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    Auth::login($user);

    // Sync with Filament Site guard
    if (! Auth::guard('filament_site')->check()) {
        Auth::guard('filament_site')->login($user);
    }

    return redirect()->to('/app');
})->name('register.custom');

/*
|--------------------------------------------------------------------------
| ⚠️ Note
|--------------------------------------------------------------------------
| Do NOT define /order route here — Filament Site handles it.
|
*/

require __DIR__ . '/auth.php';
