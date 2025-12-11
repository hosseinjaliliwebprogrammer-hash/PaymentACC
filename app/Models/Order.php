<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    /**
     * فیلدهایی که مجاز به پر شدن هستند
     */
    protected $fillable = [
        'user_id',
        'product_id',            // ✅ محصول انتخاب‌شده
        'gateway_id',            // ✅ درگاه تخصیص‌داده‌شده
        'name',
        'email',
        'service',               // اگر دیگر استفاده نمی‌کنی، بعداً می‌توانی حذفش کنی
        'amount',
        'description',
        'status',
        'tracking_code',
        'response',
        'payment_instructions',  // ✅ JSON دستور پرداخت
        'expire_at',
        'send_at',

        // 🔽🔽 فیلدهای تحویل سفارش (جدید) 🔽🔽
        'delivery_username',
        'delivery_password',
        'delivery_server',
        'delivery_notes',
    ];

    /**
     * نوع داده‌ها
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_instructions' => 'array', // 👈 آرایه JSON برای دستور پرداخت
        'response' => 'array',
    ];

    /**
     * (اختیاری) اگر می‌خوای هنگام toArray()/JSON این فیلد هم بیاد، این خط رو آنکامنت کن
     */
    // protected $appends = ['public_payment_instructions'];

    /**
     * مقادیر پیش‌فرض در زمان ساخت
     */
    protected static function booted(): void
    {
        static::creating(function (self $order) {
            if (empty($order->tracking_code)) {
                $order->tracking_code = Str::upper(Str::random(10));
            }
            if (empty($order->status)) {
                $order->status = 'pending';
            }
        });
    }

    // ----------------
    // روابط بین مدل‌ها
    // ----------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Gateway::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // -----------------------------------------
    // Accessors
    // -----------------------------------------

    /**
     * نسخهٔ عمومی و امن از دستور پرداخت برای نمایش به کاربر.
     * استفاده:
     *   $order->public_payment_instructions
     *   $safe = $order->public_payment_instructions;
     *   $safe['email_body'] ?? null
     */
    public function getPublicPaymentInstructionsAttribute(): array
    {
        $pi = $this->payment_instructions ?? [];
        $mode = data_get($pi, 'delivery_mode');

        return [
            'provider'      => data_get($pi, 'provider'),
            'display'       => data_get($pi, 'display'),
            'amount'        => data_get($pi, 'amount', $this->amount),
            'note'          => data_get($pi, 'note'),
            'email_subject' => data_get($pi, 'email_subject'),
            'email_body'    => data_get($pi, 'email_body'),
            // فقط اگر لازم است به کاربر نشان داده شود:
            'email'         => $mode === 'email' ? data_get($pi, 'email') : null,
            'url'           => $mode === 'url'   ? data_get($pi, 'url')   : null,
            'delivery_mode' => $mode,
        ];
    }
}
