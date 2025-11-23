<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
class ProductVariant extends Model
{
    use HasTranslations;
    protected $fillable = [ 'price', 'manage_quantity', 'quantity', 'product_id','name','drawbacks','highlights','reserved_stock'];
    protected $casts = [
        'name' => 'array',
        'highlights'=>"array",
        'drawbacks'=>"array"

    ];
        public $translatable = ['name','highlights','drawbacks'];

    public function variantImages()
    {
        return $this->hasMany(VariantImage::class, 'product_variant_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'product_variant_attribute_value'
        );
    }
    public function attributeValuesPivot(){
    return $this->hasMany(ProductVariantAttributeValue::class, 'product_variant_id');
}

protected function actualPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                $product = $this->product; // جلب بيانات المنتج الأب

                // 1. تحقق من أن المنتج الأب لديه متغيرات لضمان عدم وجود تناقض
                if (!$product->has_variants) {
                    return $this->price; // إذا لم يكن لديه متغيرات، هذا المتغير لا يجب أن يُرى.
                }

                // 2. التحقق من حالة الخصم المجدول في المنتج الأب
                $isDiscountActive = $product->has_discount &&
                    $product->discount_percentage > 0 &&
                    Carbon::parse($product->start_at)->lte(Carbon::now()) &&
                    Carbon::parse($product->ends_at)->gte(Carbon::now());

                if ($isDiscountActive) {
                    $discountPercentage = $product->discount_percentage / 100;
                    // الخصم يُطبق على حقل 'price' في جدول المتغيرات
                    $discountAmount = $this->price * $discountPercentage;
                    return round($this->price - $discountAmount, 2);
                }

                // إذا لم يكن الخصم فعالاً، عد للسعر الأساسي للمتغير
                return $this->price;
            },
        );
    }
    public function stockAdjustments()
{
    return $this->morphMany(StockAdjustmentTransaction::class, 'adjustable');
}
}
