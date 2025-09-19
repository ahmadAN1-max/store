<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id', 'product_id', 'child_id', 'quantity', 'price','is_return',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}
public function child()
{
    return $this->belongsTo(Product::class, 'child_id');
}

}
