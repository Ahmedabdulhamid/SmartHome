<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
       protected $guarded = [
       'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function paymMethod()
    {
        return $this->belongsTo(PaymMethod::class);
    }
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
    public function sale()
{
    return $this->hasOne(Sale::class);
}
}
