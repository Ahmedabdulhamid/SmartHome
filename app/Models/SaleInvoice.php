<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleInvoice extends Model
{
     protected $table = 'sales_invoices';
    protected $guarded = ['id'];
    protected $dates = ['issued_at', 'paid_at'];
       public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
