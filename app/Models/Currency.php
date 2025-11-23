<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'precision',
        'decimal_mark',
        'thousands_separator',
        'symbol_first',
        'active',
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
    public function rfqs()
    {
        return $this->hasMany(RFQ::class);
    }
}
