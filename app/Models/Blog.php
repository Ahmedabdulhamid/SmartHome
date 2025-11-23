<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Facades\Cookie;

class Blog extends Model
{
    use HasTranslations, HasSlug;
    protected $guarded = ['id'];
    public $translatable = ['title', 'excerpt', 'content', 'meta_description'];

    protected $casts = [
        'title' => 'array',
        "excerpt" => "array",
        'content' => 'array',
        'meta_description' => "array",
        'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'published_at' => 'datetime',
    ];
    public function parent()
    {
        return $this->belongsTo(Blog::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Blog::class, 'parent_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function author()
    {
        return $this->belongsTo(Admin::class, 'author_id'); // افترضنا أن الكاتب هو User
    }
    public function incrementViews(): void
    {
        $cookieName = 'blog_viewed_' . $this->id;
        $expiryMinutes = 1440; // 24 ساعة (24 * 60)

        // التحقق مما إذا كان الكوكي موجودًا (أي تمت مشاهدته خلال الـ 24 ساعة الماضية)
        if (!Cookie::has($cookieName)) {

            // زيادة عداد المشاهدات في قاعدة البيانات
            $this->increment('views_count');

            // إنشاء كوكي ينتهي بعد 24 ساعة (1440 دقيقة)
            Cookie::queue($cookieName, true, $expiryMinutes);
        }
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
