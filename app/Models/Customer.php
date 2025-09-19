<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
      protected $table = 'customers'; // عادة ما يكون هذا الاسم تلقائياً

    protected $fillable = [
        'name',
        'phone',
        'city',
        'address',
    ];
}
