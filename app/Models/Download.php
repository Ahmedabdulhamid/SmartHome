<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Download extends Model
{
    use HasTranslations, HasSlug;
    protected $fillable = ['title', 'type', 'file_path'];

    public $translatable = ['title'];
    protected $casts = [
        'title' => 'array',
    ];
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                return $model->getTranslation('title', 'en'); // slug دايمًا من الإنجليزي
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate(); // slug يتولد مرة واحدة فقط
    }
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
