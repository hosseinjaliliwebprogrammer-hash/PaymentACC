<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaypalPaymentSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'transaction_id',
        'paypal_email',
        'screenshot_path',
        'status',
    ];

    // روابط برای نمایش در جدول ادمین
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }
}
