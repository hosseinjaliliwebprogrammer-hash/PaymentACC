<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\EmailTemplate;

class Gateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'link',
        'invoice_description',
        'email_template_type',
        'max_transactions',
        'limit_amount',
        'used_amount',
        'is_active',
        'logo',
        'priority',
        // برای استفاده‌های بعدی (وقتی ستون را اضافه کردیم)
        'email_template_id',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'limit_amount'  => 'decimal:2',
        'used_amount'   => 'decimal:2',
        'priority'      => 'integer',
    ];

    /**
     * Relation: Gateway has many Orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Select an active Gateway that has enough remaining limit
     */
    public static function pickForAmount(float $amount): ?Gateway
    {
        $gateways = self::query()
            ->where('is_active', true)
            ->orderByDesc('priority') // اولویت بالاتر جلوتر
            ->orderByRaw('RAND()')    // اگر اولویت برابر بود، تصادفی
            ->get();

        foreach ($gateways as $gateway) {
            // 0 یعنی نامحدود
            if ((float) $gateway->limit_amount === 0.0) {
                return $gateway;
            }

            // اگر هنوز ظرفیت دارد
            if (($gateway->used_amount + $amount) <= $gateway->limit_amount) {
                return $gateway;
            }
        }

        return null; // هیچ گیت‌وی واجد شرایط نبود
    }

    /**
     * Increment the used_amount after an order is created
     */
    public function addUsage(float $amount): void
    {
        $this->increment('used_amount', $amount);
    }

    /**
     * Check if Gateway can handle this amount
     */
    public function hasCapacityFor(float $amount): bool
    {
        return $this->is_active && (
            $this->limit_amount == 0 ||
            ($this->used_amount + $amount) <= $this->limit_amount
        );
    }

    /**
     * Email template relation (optional)
     */
    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'email_template_id');
    }
}
