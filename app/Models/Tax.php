<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'type',
        'rate',
    ];

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
