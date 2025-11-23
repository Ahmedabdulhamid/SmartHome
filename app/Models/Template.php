<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
class Template extends Model
{
   use HasTranslations, HasSlug;
       protected $guarded = ['id'];
       protected $translatable=['name','header_html','body_html','footer_html'];
       protected $casts=['name'=>"array",'header_html'=>"array",'body_html'=>"array",'footer_html'=>"array"];
    public function getSlugOptions(): SlugOptions
    {

        return SlugOptions::create()
            ->generateSlugsFrom(function ($model) {
                return $model->getTranslation('name', 'en'); // slug دايمًا من الإنجليزي
            })
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
            // slug يتولد مرة واحدة فقط
    }
     public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
