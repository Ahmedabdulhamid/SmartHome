<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
     use HasTranslations,HasSlug;
     protected $fillable=['name','slug','parent_id','type'];

    public $translatable = ['name'];


     protected $casts = [
        'name' => 'array',
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                return $model->getTranslation('name', 'en'); // slug دايمًا من الإنجليزي
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate(); // slug يتولد مرة واحدة فقط
    }
        public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
        public function getRouteKeyName(): string
{
    return 'slug';
}

    /**
     * التصنيفات الفرعية
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
