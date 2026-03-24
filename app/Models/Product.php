<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasTranslations, HasSlug;
    protected $fillable =
    [
        'name',
        'description',
        'category_id',
        'brand_id',
        'has_variants',
        'base_price',
        'manage_quantity',
        'quantity',
        'slug',
        'status',
        'has_discount',
        'discount_percentage',
        'currency_id',
        'start_at',
        'ends_at',
        'highlights',
        'drawbacks',
        'reserved_stock'
    ];
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'highlights' => "array",
        'drawbacks' => "array"

    ];
    public $translatable = ['name', 'description', 'highlights', 'drawbacks'];
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                return $model->getTranslation('name', 'en'); // slug دايمًا من الإنجليزي
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate(); // slug يتولد مرة واحدة فقط
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    public function minVariantPrice()
    {
        return $this->variants()->min('price');
    }
    public function firstImage()
    {
        return $this->hasOne(Image::class)->oldestOfMany();
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes');
    }
    public function dataSheets()
    {
        return $this->hasMany(DataSheet::class);
    }

    protected function actualPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                // إذا كان المنتج يحتوي على متغيرات، يجب أن يعرض نطاق أسعار
                if ($this->has_variants) {
                    // يمكنك جلب أقل سعر مخصوم من المتغيرات إذا أردت
                    // For now, we return null or range, letting the frontend handle variants.
                    return null;
                }

                // --- منطق الخصم للمنتجات البسيطة (Simple Products) ---
                $isDiscountActive = $this->has_discount &&
                    $this->discount_percentage > 0 &&
                    Carbon::parse($this->start_at)->lte(Carbon::now()) &&
                    Carbon::parse($this->ends_at)->gte(Carbon::now());

                if ($isDiscountActive) {
                    $basePrice = $this->base_price; // استخدام base_price أو price حسب حقل السعر الأصلي لديك
                    $discountAmount = $basePrice * ($this->discount_percentage / 100);
                    // يجب تقريب السعر النهائي لرقمين عشريين
                    return round($basePrice - $discountAmount, 2);
                }


                return $this->base_price; // استخدام base_price أو price
            },
        );
    }
    public function stockAdjustments()
    {
        return $this->morphMany(StockAdjustmentTransaction::class, 'adjustable');
    }
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_item')
            ->using(CartItem::class)
            ->withPivot('quantity', 'product_variant_id', 'price');
    }
    protected static function booted()
    {
        static::deleting(function ($product) {
            // حذف صور المنتج
            if ($product->images) {
                foreach ($product->images as $image) {
                    if ($image->path && Storage::disk('public')->exists($image->path)) {
                        Storage::disk('public')->delete($image->path);
                    }
                }
            }

            // حذف ملفات الـ Data Sheets
            if ($product->dataSheets) {
                foreach ($product->dataSheets as $dataSheet) {
                    if ($dataSheet->file_path && Storage::disk('public')->exists($dataSheet->file_path)) {
                        Storage::disk('public')->delete($dataSheet->file_path);
                    }
                }
            }
            if( $product->variants ){
                foreach( $product->variants as $variant ){
                    foreach( $variant->variantImages as $vImage ){
                        if ( $vImage->path && Storage::disk('public')->exists($vImage->path) ) {
                            Storage::disk('public')->delete( $vImage->path );
                        }
                    }
                }
            }
        });
        static::updating(function ($product) {
            // جلب النسخة القديمة من الـ model
            $original = $product->getOriginal();

            // حذف صور تم تغيير مسارها
            if ($product->images) {
                foreach ($product->images as $image) {
                    $oldPath = $original['images'] ?? null;
                    if ($oldPath && $oldPath !== $image->path && Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }
            if( $product->variants ){
                foreach( $product->variants as $variant ){
                    foreach( $variant->variantImages as $vImage ){
                        $oldPath = $original['variantImages'] ?? null;
                        if ($oldPath && $oldPath !== $vImage->path && Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
            }

            // حذف ملفات الـ Data Sheets القديمة
            if ($product->dataSheets) {
                foreach ($product->dataSheets as $dataSheet) {
                    $oldFilePath = $original['dataSheets'] ?? null;
                    if ($oldFilePath && $oldFilePath !== $dataSheet->file_path && Storage::disk('public')->exists($oldFilePath)) {
                        Storage::disk('public')->delete($oldFilePath);
                    }
                }
            }
        });
    }
}
