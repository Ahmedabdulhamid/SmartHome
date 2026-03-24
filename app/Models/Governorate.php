<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Governorate extends Model
{
    use HasTranslations, HasSlug;
    protected $table = 'governorates';
    protected $fillable = ['name', 'status','slug'];
    public $translatable = ['name'];

    protected $casts = [
        'name' => 'array',
    ];
    public function cities()
    {
        return $this->hasMany(City::class);
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
