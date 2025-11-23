<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AdditionalCost extends Model
{
    protected $fillable = [
        'name',
        'value',
        'show_to_customer',
        'is_standard',
    ];

    public function quotationLinks()
    {
        return $this->hasMany(QuotationAdditionalCost::class);
    }
public function getFinalValue($quotationId)
{
    $hiddenSubs = $this->quotationLinks()
        ->where('quotation_id', $quotationId)
        ->where('show_to_customer', false)
        ->sum('custom_value');

    return $this->value + $hiddenSubs;
}

}
