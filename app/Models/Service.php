<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasTranslations, HasSlug, HasFactory;

    protected $table = "services";

    protected $fillable = [
        'title',
        'short_description',
        'description',
        'slug',
        'image',
        'icon',
        'base_price',
        'currency_id',      // تم إضافته لـ fillable
        'meta_description',
        'is_active',
        'sort_order',       // تم إضافته لـ fillable
        "category_id"
    ];


    protected $casts = [
        'is_active' => "boolean",
        'base_price' => 'decimal:2',
        'sort_order' => 'integer', // تأكد من تحويله كعدد صحيح
    ];

    // =======================================================
    // 2. الحقول المترجمة (Translatable)
    // =======================================================

    public array $translatable = [
        'title',
        'short_description',
        'description',
        'meta_description', // ممتاز، تم تعريفه هنا للترجمة
    ];

    // =======================================================
    // 3. علاقات قاعدة البيانات (Relationships)
    // =======================================================

    /**
     * ربط الخدمة بفئة واحدة.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * ربط الخدمة بالعملة الأساسية لسعرها.
     */
    public function baseCurrency(): BelongsTo
    {
        // ربط السعر الأساسي للخدمة بعملة واحدة من جدول currencies
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * ربط الخدمة بالميزات المتعددة (Many-to-Many).
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'feature_service', 'service_id', 'feature_id')
                    ->withPivot(['id', 'additional_cost', 'currency_id'])
                    ->using(FeatureService::class);
    }


    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                return $model->getTranslation('title', 'en');
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }
}
