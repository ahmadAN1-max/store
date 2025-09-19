<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_category', 'category_id', 'coupon_id');
    }
}
