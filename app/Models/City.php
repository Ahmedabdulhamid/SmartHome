<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
class City extends Model
{
    use HasTranslations, HasSlug;
    protected $table = 'cities';
    protected $fillable = ['name', 'governorate_id','slug','status'];
    public $translatable = ['name'];
     protected $casts = [
        'name' => 'array',
    ];
    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
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
}
