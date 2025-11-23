<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class StockItem extends Model
{
     use HasTranslations;
     public $translatable = ['item_name'];
      protected $casts = [
        'item_name' => 'array',


    ];
     protected $table = 'stock_items';
    // لا نحتاج إلى حقل updated_at
    public $timestamps = false;
    // يجب تحديد المفتاح الرئيسي هنا (سيكون مركبًا في الـ View، لكن نستخدم entity_id للعرض)
    protected $primaryKey = 'entity_id';
    public $incrementing = false; // لأن الـ View ليس جدولاً حقيقياً

    // لربط الـ View بالـ Model الحقيقي للتعديل
    public function adjustable()
    {
        return $this->morphTo(__FUNCTION__, 'entity_type', 'entity_id');
    }
    // App\Models\StockItem.php

// ...
protected function itemName(): Attribute
{
    return Attribute::make(
        get: function ($value, $attributes) {
            $adjustable = $this->adjustable;

            if ($adjustable) {
                // ⚠️ يجب أن يكون حقل 'name' translatable في Product و ProductVariant
                if ($attributes['entity_type'] === 'App\Models\Product') {
                    // Spatie تقوم بالترجمة تلقائياً هنا
                    return $adjustable->name;
                } else {
                    // Spatie تقوم بالترجمة تلقائياً هنا أيضاً
                    $productName = $adjustable->product->name;
                    $variantName = $adjustable->name ?? 'N/A';

                    return "{$productName} - {$variantName}";
                }
            }
            return 'غير متوفر';
        },
    );
}
// ...
}
