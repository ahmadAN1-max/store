<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
protected $fillable = [
        'bill_number',
        'name',
        'phone_number',
        'reference',
        'status',
        'total_price',
        'payment_method',
        'total_items',  // لازم يكون هنا
        'user_id',
        'employee_name',
    ];

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billItems()
    {
        return $this->hasMany(BillItem::class);
    }
}
