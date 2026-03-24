<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $table = 'sales_items';
    protected $guarded = ['id'];
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
