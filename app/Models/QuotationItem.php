<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'base_price',
        'selling_price',
        'subtotal',
        'margin_percentage',
        'final_price',
        'tax_id',
        'rfq_item_id'
    ];

    public function rfqItem()
    {
        return $this->belongsTo(RfqItem::class, 'rfq_item_id');
    }
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
    public function getFinalPrice()
    {
        $totalSellingPrice = $this->selling_price * $this->quantity;
        $taxAmount = ($this->tax?->rate) / 100 * $totalSellingPrice;
        return $totalSellingPrice + $taxAmount;
    }
    protected static function booted()
    {
        static::creating(function ($item) {
            $product = $item->product;
            $variant = $item->variant;

            // 1️⃣ حساب base_price
            if ($product->has_variants) {
                $item->base_price = $variant
                    ? ($product->has_discount
                        ? $variant->price * (1 - $product->discount_percentage / 100)
                        : $variant->price)
                    : ($product->has_discount
                        ? $product->base_price * (1 - $product->discount_percentage / 100)
                        : $product->base_price ?? 0);
            } else {
                $item->base_price = $product->has_discount
                    ? $product->base_price * (1 - $product->discount_percentage / 100)
                    : $product->base_price ?? 0;
            }

            // 2️⃣ حساب selling_price من base_price + margin
            $margin = $item->margin_percentage ?? 0;
            $item->selling_price = $item->base_price + ($item->base_price * $margin / 100);

            // 3️⃣ Subtotal (بدون ضرائب أو إضافات)
            $item->subtotal = $item->selling_price * $item->quantity;
            $taxAmount = 0;

            if ($item->tax) {
                $taxAmount = ($item->selling_price * $item->tax->rate) / 100;
            }



            // 5️⃣ Final Price = (selling + tax) * quantity

            // Log::info('Tax Amount: ' . $taxAmount);
        });


        static::saved(function ($item) {
            // تحديث الـ total للـ quotation
            $item->quotation->recalcTotal();
        });

        static::deleted(function ($item) {
            // لو تم حذف العنصر، نعيد حساب الـ total
            $item->quotation->recalcTotal();
        });
    }
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
