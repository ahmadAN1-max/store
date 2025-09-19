<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
public function categories()
{
    return $this->belongsToMany(
        Category::class,
        'coupon_category',
        'coupon_id',
        'category_id'
    );
}


}
