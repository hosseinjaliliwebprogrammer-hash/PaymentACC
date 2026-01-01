<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;

class Product extends Model
{
    protected $fillable = ['name','sku','price','is_active','description'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($q) { return $q->where('is_active', true); }

    public function discountCodes()
    {
        return $this->belongsToMany(DiscountCode::class, 'discount_code_product');
    }


    /**
     * محاسبه قیمت با تخفیف
     *
     * @param string|null $discountCode
     * @return float
     */
    public function getDiscountedPrice($discountCode = null)
    {

        if ($discountCode) {
            $discount = DiscountCode::where('code', $discountCode)
                ->where('is_active', true)
                ->where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->first();

            if ($discount) {
                $discountedPrice = $this->price * ((100 - $discount->discount_percentage) / 100);
                return $discountedPrice;
            }
        }

        return $this->price;
    }
}
