<?php

namespace App\Filament\Site\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\PaypalPaymentSubmission;

// 🟢 ایمپورت ارسال ایمیل
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmedMail;

class SubmitPaypalPayment extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false;

    // 🔥 مهم: مسیر صفحه برای رفع 404
    protected static ?string $slug = 'submit-paypal-payment';

    // ویو صفحه
    protected static string $view = 'filament.site.pages.submit-paypal-payment';

    public ?Order $order = null;

    public $transaction_id;
    public $paypal_email;
    public $screenshot;

    public function mount(): void
    {
        $orderId = request()->get('order');

        if (!$orderId) {
            abort(404);
        }

        $this->order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    public function submit()
    {
        $validated = $this->validate([
            'transaction_id' => 'required|string|max:255',
            'paypal_email'   => 'required|email|max:255',
            'screenshot'     => 'nullable|image|max:2048',
        ]);

        $screenshotPath = null;

        if ($this->screenshot) {
            $screenshotPath = $this->screenshot->store('paypal_screenshots', 'public');
        }

        PaypalPaymentSubmission::create([
            'user_id'         => Auth::id(),
            'order_id'        => $this->order->id,
            'transaction_id'  => $this->transaction_id,
            'paypal_email'    => $this->paypal_email,
            'screenshot_path' => $screenshotPath,
            'status'          => 'pending',
        ]);

        // تغییر وضعیت سفارش
        $this->order->update([
            'status' => 'payment_review',
        ]);

        // 🟢 ارسال ایمیل تایید پرداخت به کاربر
        Mail::to($this->order->email)->send(new PaymentConfirmedMail($this->order));

        // 🔥 ریدایرکت به صفحه پردازش پرداخت
        return redirect()->route('filament.site.pages.payment-processing');
    }
}
