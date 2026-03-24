<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Sale extends Model
{

    protected $table='sales';
    protected $guarded=['id'];

       protected function casts(): array
    {
        return [
            'sold_at' => 'datetime',

        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
      public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
     public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
       public function invoice()
    {
        return $this->hasOne(SaleInvoice::class);
    }
    public function order(){
        return $this->belongsTo(Order::class);
    }
    protected $dates = ['issued_at', 'paid_at'];
}
