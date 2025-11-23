<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
class Brand extends Model
{
    use HasTranslations,HasSlug;
    protected $fillable = ['name', 'slug', 'logo'];
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
                return $model->getTranslation('name', 'en'); // slug من الاسم الإنجليزي
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }
        public function getRouteKeyName(): string
{
    return 'slug';
}

}
