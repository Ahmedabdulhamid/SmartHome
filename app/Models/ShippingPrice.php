<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPrice extends Model
{
    protected $table = 'shipping_prices';
    protected $fillable = [
        'governorate_id',
        'city_id',
        'shipping_type',
        'estimated_days',
        'price',
        'min_weight',
        'max_weight',
        'return_fee',
        'currency_id',
    ];
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
