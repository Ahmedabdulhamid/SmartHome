<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CartItem extends Pivot
{
    protected $table = 'cart_item';

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price'
    ];
}
