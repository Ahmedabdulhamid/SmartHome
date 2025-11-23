<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class QuotationAdditionalCost extends Model
{
    protected $table = 'quotation_additional_cost';

    protected $fillable = [
        'quotation_id',
        'additional_cost_id',
        'custom_name',
        'custom_value',
        'show_to_customer',
        'save_as_main',  // ✅ العمود موجود في DB
    ];

    protected $casts = [
        'save_as_main' => 'boolean',
        'show_to_customer' => 'boolean',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function additionalCost()
    {
        return $this->belongsTo(AdditionalCost::class);
    }

    public function getValueAttribute()
    {
        return $this->custom_value ?? $this->additionalCost?->value ?? 0;
    }

    public function getDisplayNameAttribute()
    {
        return $this->custom_name ?? $this->additionalCost?->name ?? '';
    }

    protected static function booted()
    {
        static::created(function ($subCost) {
            Log::info('تم إضافة بند فرعي جديد', [
                'quotation_id' => $subCost->quotation_id,
                'custom_name' => $subCost->custom_name,
                'custom_value' => $subCost->custom_value,
                'show_to_customer' => $subCost->show_to_customer,
                'save_as_main' => $subCost->save_as_main,
            ]);

            if ($subCost->save_as_main) {
                AdditionalCost::create([
                    'name' => $subCost->custom_name ?? $subCost->additionalCost?->name,
                    'value' => $subCost->custom_value ?? $subCost->additionalCost?->value,
                    'show_to_customer' => $subCost->show_to_customer,
                    'is_standard' => true,
                ]);
            }
        });
    }
    public function getFinalValueAttribute()
    {
        $hiddenSubs = $this->where('quotation_id', $this->quotation_id)
                           ->where('show_to_customer', false)
                           ->sum('custom_value');

        return $this->custom_value + $hiddenSubs;
    }
}
