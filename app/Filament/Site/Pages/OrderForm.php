<?php

namespace App\Filament\Site\Pages;

use App\Models\DiscountCode;
use App\Models\Gateway;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\GatewaySelector;
use App\Services\NowPaymentsService;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class OrderForm extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $title = 'Choose your service';
    protected static ?string $slug = 'order';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.site.pages.order-form';

    public ?array $data = [];
    public int $step = 1;

    public int $captchaNumber;

    // 🔥 فقط برای نمایش وضعیت وجود داشتن ایمیل
    public bool $emailExists = false;

    /**
     * 🔍 چک ایمیل (برای AJAX / Livewire)
     */
    public function checkEmail(): void
    {
        // فقط برای یوزر مهمان مهم است
        if (Auth::check()) {
            $this->emailExists = false;
            return;
        }

        $email = $this->data['email'] ?? null;

        if (empty($email)) {
            $this->emailExists = false;
            return;
        }

        $this->emailExists = User::where('email', $email)->exists();
    }

    public function nextStep()
    {
        $this->resetErrorBag();

        $rules = [
            'data.product_id' => ['required', 'exists:products,id'],
            'data.captcha'    => ['required', 'in:' . $this->captchaNumber],
        ];

        if (! Auth::check()) {
            $rules = array_merge($rules, [
                'data.name'     => ['required'],
                'data.email'    => ['required', 'email'],
                'data.password' => ['required', 'min:6'],
            ]);
        }

        $this->validate($rules);
        $this->step = 2;
    }

    public function previousStep()
    {
        $this->step = 1;
    }

    public function mount(): void
    {
        if(request()->input('discount')) {
            Cache::put('discount_code', request()->input('discount'), 3600);
        }

        $this->captchaNumber = random_int(10000, 99999);

        $this->form->fill([
            'name'           => Auth::user()->name ?? null,
            'email'          => Auth::user()->email ?? null,
            'payment_method' => 'paypal',
            'captcha'        => null,
        ]);

        // اگر مهمان هست و ایمیل قبلاً داخل state باشد، وضعیت اولیه را چک کن
        if (! Auth::check() && ! empty($this->data['email'])) {
            $this->emailExists = User::where('email', $this->data['email'])->exists();
        }
    }

    public function form(Form $form): Form
    {
        $isGuest = ! Auth::check();

        return $form->schema([
            TextInput::make('name')
                ->label('Your Name')
                ->visible($isGuest),

            TextInput::make('email')
                ->label('Your Email Address')
                ->visible($isGuest)
                ->live() // 🔥 هر تغییر را Livewire بگیرد
                ->afterStateUpdated(fn () => $this->checkEmail()), // 🔥 ایمیل را چک کنیم

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->dehydrated(fn ($state) => filled($state))
                ->visible($isGuest),

            Select::make('product_id')
                ->label('Product')
                ->options(function () {
                    $discountCode = Cache::get('discount_code');

                    // اگر کد تخفیف موجود بود، بررسی می‌کنیم که محصولی برای اون کد وجود داره یا نه
                    $products = $discountCode
                        ? Product::whereHas('discountCodes', function($query) use ($discountCode) {
                            $query->where('code', $discountCode);
                        })->pluck('name', 'id')
                        : collect(); // در صورتی که کد تخفیف نباشه یک مجموعه خالی می‌سازیم

                    // اگر محصولی برای کد تخفیف پیدا نشد، به صورت پیش‌فرض محصولات فعال رو بارگذاری می‌کنیم
                    if ($products->isEmpty()) {
                        $products = Product::where('is_active', true)->pluck('name', 'id');
                    }

                    return $products;
                })
                ->searchable()
                ->preload()
                ->live()
                ->afterStateUpdated(fn ($state, Set $set) =>
                $set('amount', $state ? (float) Product::find($state)->getDiscountedPrice(Cache::get('discount_code')) : null)
                ),

            TextInput::make('amount')
                ->label('Amount (USD)')
                ->numeric()
                ->readOnly(),

            TextInput::make('captcha')
                ->label(fn () => "Security Check — please type the number ({$this->captchaNumber}) below")
                ->placeholder(fn () => "Enter {$this->captchaNumber}")
                ->required()
                ->visible(fn () => $this->step === 1),

            Radio::make('payment_method')
                ->label('Payment Method')
                ->options([
                    'paypal'  => 'PayPal',
                    'paygate' => 'Paygate (Crypto/Card)',
                    'nowpay'  => 'NOWPayments (Crypto)',
                ])
                ->inline()
                ->visible(fn () => $this->step === 2),

            Forms\Components\View::make('filament.site.components.payment-info')
                ->visible(fn () => $this->step === 1),
        ])
            ->statePath('data');
    }

    public function submit(): mixed
    {
        $this->resetErrorBag();

        Log::debug('OrderForm::submit fired', [
            'payment_method' => $this->data['payment_method'] ?? null,
        ]);

        $data = $this->form->getState();
        $isGuest = ! Auth::check();

        $discount = Cache::get('discount_code');
        $requiredFields = ['product_id', 'amount', 'payment_method'];
        if ($isGuest) {
            $requiredFields = array_merge($requiredFields, ['name', 'email']);
        }

        foreach ($requiredFields as $f) {
            if (empty($data[$f])) {
                throw ValidationException::withMessages([
                    $f => ucfirst(str_replace('_', ' ', $f)) . ' is required.',
                ]);
            }
        }

        if ($isGuest && empty($data['password'])) {
            throw ValidationException::withMessages([
                'password' => 'Password is required.',
            ]);
        }

        $product = Product::findOrFail($data['product_id']);
        $amount  = (float) $product->price;
        if($product && $discount)
            $amount = $product->getDiscountedPrice($discount);


        // create/login user
        $user = Auth::user();
        if (! $user) {
            $existing = User::where('email', $data['email'])->first();
            if ($existing) {
                if (! Auth::attempt(['email' => $data['email'], 'password' => $data['password']], true)) {
                    throw ValidationException::withMessages([
                        'password' => 'Incorrect password.',
                    ]);
                }
                $user = Auth::user();
            } else {
                $user = User::create([
                    'name'     => $data['name'],
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
                Auth::login($user, true);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | NOWPAYMENTS — Fixed Clean Version
        |--------------------------------------------------------------------------
        */
        if ($data['payment_method'] === 'nowpay') {

            $tracking = Str::ulid()->toBase32();

            $order = Order::create([
                'user_id'       => $user->id,
                'product_id'    => $product->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'amount'        => $amount,
                'status'        => 'pending',
                'tracking_code' => $tracking,
                'service'       => $product->name,
                'discount_code' => $discount
            ]);

            // build domain-based URLs
            $successUrl = url("/payment/success?order_id={$order->id}");
            $cancelUrl  = url("/payment/failed?order_id={$order->id}");

            $now = new NowPaymentsService();

            try {
                $invoice = $now->createInvoice($order, $successUrl, $cancelUrl);

                // ALWAYS use official invoice_url
                $invoiceUrl = $invoice['invoice_url'] ?? null;

                $order->update([
                    'payment_instructions' => [
                        'provider'    => 'nowpay',
                        'invoice_id'  => $invoice['id'] ?? null,
                        'invoice_url' => $invoiceUrl,
                        'status'      => $invoice['invoice_status'] ?? ($invoice['status'] ?? null),
                    ],
                ]);

                if ($invoiceUrl) {
                    return redirect()->away($invoiceUrl);
                }

            } catch (\Throwable $e) {
                Log::error('NOWPayments invoice error', [
                    'order_id' => $order->id,
                    'message'  => $e->getMessage(),
                ]);

                Notification::make()
                    ->title('NOWPayments is temporarily unavailable')
                    ->danger()
                    ->send();

                return redirect()->to('/app/order');
            }

            return redirect()->to($successUrl);
        }

        /*
        |--------------------------------------------------------------------------
        | PAYGATE — فقط همین بخش اصلاح شده
        |--------------------------------------------------------------------------
        */
        if ($data['payment_method'] === 'paygate') {
            $wallet          = env('PAYGATE_RECEIVE_ADDRESS');
            $tracking        = Str::ulid()->toBase32();
            $callback        = route('paygate.return',['tracking' => $tracking]);
            $amountFormatted = number_format($amount, 2, '.', '');

            // استفاده از BASE URL از .env
            $apiBase = rtrim(env('PAYGATE_BASE_URL', 'https://api.paygate.to'), '/');

            $walletResponse = Http::get($apiBase . '/control/wallet.php', [
                'address'  => $wallet,
                'callback' => $callback,
            ]);

            $walletData = $walletResponse->json();

            if (! isset($walletData['address_in'])) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Paygate API error.',
                ]);
            }

            $addressIn = $walletData['address_in'];

            // استفاده از دامنه و provider از .env
            $checkoutDomain = env('PAYGATE_CHECKOUT_DOMAIN', 'checkout.paygate.to');
            $provider       = env('PAYGATE_PROVIDER', 'moonpay');

            $paymentUrl = sprintf(
                'https://%s/process-payment.php?address=%s&amount=%s&provider=%s&email=%s&currency=USD',
                $checkoutDomain,
                $addressIn,
                $amountFormatted,
                $provider,
                $user->email
            );

            Order::create([
                'user_id'       => $user->id,
                'product_id'    => $product->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'amount'        => $amount,
                'status'        => 'pending',
                'tracking_code' => $tracking,
                'service'       => $product->name,
                'discount_code' => $discount,
                'payment_instructions' => [
                    'provider'    => 'paygate',
                    'wallet'      => $wallet,
                    'address_in'  => $addressIn,
                    'payment_url' => $paymentUrl,
                ],
            ]);

            return redirect()->away($paymentUrl);
        }
        if ($data['payment_method'] === 'card2crypto') {
            $wallet          = env('CARD2CRYPTO_RECEIVE_ADDRESS');
            $tracking        = Str::ulid()->toBase32();
            $callback        = route('card2crypto.return',['tracking' => $tracking]);
            $amountFormatted = number_format($amount, 2, '.', '');

            // استفاده از BASE URL از .env
            $apiBase = rtrim(env('CARD2CRYPTO_BASE_URL'), '/');

            $walletResponse = Http::get($apiBase . '/control/wallet.php', [
                'address'  => $wallet,
                'callback' => $callback,
            ]);

            $walletData = $walletResponse->json();

            if (! isset($walletData['address_in'])) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Card2crypto API error.',
                ]);
            }

            $addressIn = $walletData['address_in'];

            // استفاده از دامنه و provider از .env
            $checkoutDomain = env('CARD2CRYPTO_CHECKOUT_DOMAIN', 'checkout.paygate.to');
            $provider       = env('CARD2CRYPTO_PROVIDER', 'moonpay');

            $paymentUrl = sprintf(
                'https://%s/pay.php?address=%s&amount=%s&email=%s&currency=USD&domain=%s',
                $checkoutDomain,
                $addressIn,
                $amountFormatted,
                $user->email,
                $checkoutDomain
            );

            Order::create([
                'user_id'       => $user->id,
                'product_id'    => $product->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'amount'        => $amount,
                'status'        => 'pending',
                'tracking_code' => $tracking,
                'service'       => $product->name,
                'discount_code' => $discount,
                'payment_instructions' => [
                    'provider'    => 'card2crypto',
                    'wallet'      => $wallet,
                    'address_in'  => $addressIn,
                    'payment_url' => $paymentUrl,
                ],
            ]);

            return redirect()->away($paymentUrl);
        }

        /*
        |--------------------------------------------------------------------------
        | PAYPAL (unchanged)
        |--------------------------------------------------------------------------
        */
        $link = DB::transaction(function () use ($data, $user, $product, $amount, $discount) {


            $gateway = Gateway::where('is_active', true)
                ->whereRaw('(limit_amount - used_amount) >= ?', [$amount])
                ->orderByRaw('(limit_amount - used_amount) DESC')
                ->first();

            if (! $gateway) {
                throw ValidationException::withMessages([
                    'gateway_id' => 'No PayPal gateway available.',
                ]);
            }

            $gateway->increment('used_amount', $amount);

            $tracking = Str::ulid()->toBase32();
/*
            $emailTemplate = GatewaySelector::generateEmailTemplate($gateway, (object) [
                'name'          => $user->name,
                'amount'        => $amount,
                'product'       => $product,
                'tracking_code' => $tracking,
                'tracking_url'  => url("/app/orders/{$tracking}/summary"),
            ]);
*/

            $order = Order::create([
                'user_id'       => $user->id,
                'product_id'    => $product->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'amount'        => $amount,
                'gateway_id'    => $gateway->id,
                'status'        => 'pending',
                'tracking_code' => $tracking,
                'service'       => $product->name,
                'discount_code' => $discount,
                'payment_instructions' => [
                    'provider'   => 'paypal',
                    'amount'     => $amount,
                    'email'      => $gateway->email,
                    'url'        => $gateway->link,
                ],
            ]);

            $secretKey = env('APP_KEY'); // You can store this key in `.env`
            $payload = [
                'order_id' => $order->id,
                'user_id' => $order->user->id,
                'amount' => $amount,
                'product_id' => $product->id,
                'title' => $product->name,
                'description' => $product->description,
                'product_invoice' => $product->sku,
                'tracking'   => $tracking,
                'email' => $user->email,
                'expires_at' => Carbon::now()->addDay(7)->timestamp, // Expiry date (7 day)
                'iat' => Carbon::now()->timestamp, // Token issue time
            ];


            $jwt = JWT::encode($payload, $secretKey, 'HS256');  // 'HS256' is the default algorithm, but you should explicitly specify it.
            $link = $gateway->link . "/api/paypal?token={$jwt}";

            /*
            Mail::send('emails.paypal', [
                'name'          => $user->name,
                'amount'        => $amount,
                'service'       => $product->name,
                'gateway_email' => $gateway->email,
                'gateway_link'  => $gateway->link,
                'tracking_url'  => url("/app/orders/{$tracking}/summary"),
                'submit_url'    => url("/app/submit-paypal-payment?order={$order->id}"),
            ], function ($msg) use ($user) {
                $msg->to($user->email)->subject('Your PayPal Payment Instructions');
            });
            */
            return $link;
        });

        Notification::make()->title('Order created')->success()->send();

        return redirect()->to($link);
    }
}
