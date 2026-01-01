<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'discount_percentage', 'start_date', 'end_date', 'is_active'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'discount_code_product');
    }

}
