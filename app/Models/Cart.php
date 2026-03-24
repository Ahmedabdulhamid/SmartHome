<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'carts';
    protected $fillable = [
        'user_id',
        'session_id',
    ];
    public function items()
    {
        return $this->belongsToMany(Product::class,'cart_item')
        ->using(CartItem::class)
        ->withPivot('quantity', 'product_variant_id','price');
    }
}
