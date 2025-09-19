<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'sizes',
        'quantity',
        'regular_price',
        'sale_price',
        'brand_id',
        'SKU',
        'stock_status',
        'unit_cost',
        'short_description',
        'description',
        'barcode',
        'parent',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function children()
    {
        return $this->hasMany(Product::class, 'parent_id')->where('parent', false);
    }

    public function parentProduct()
    {
        return $this->belongsTo(Product::class, 'parent_id')->where('parent', true);
    }
}
