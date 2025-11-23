<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class InventoryTransaction extends Model
{
    // السماح بالتعبئة الجماعية لهذه الحقول
    protected $fillable = [
        'quotation_id',
        'quotation_item_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'type',
        'reference',
    ];

    // تحديد نوع الحقل Enum
    protected $casts = [
        'type' => 'string', // يمكن استخدام InventoryTransactionType::class إذا استخدمت Enums في PHP 8.1+
    ];

    /**
     * العلاقة مع عرض السعر (Order/Quotation) المصدر للحركة.
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    /**
     * العلاقة مع عنصر عرض السعر المحدد.
     */
    public function quotationItem(): BelongsTo
    {
        return $this->belongsTo(QuotationItem::class);
    }

    /**
     * العلاقة مع المنتج الذي تمت عليه الحركة.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * العلاقة مع الوحدة الفرعية (Variant) التي تمت عليها الحركة.
     */
    public function variant(): BelongsTo
    {
        // بافتراض أن اسم الموديل هو ProductVariant
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
