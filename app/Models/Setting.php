<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class Setting extends Model
{
    protected $guarded = ['id'];
    use HasTranslations;
    public $translatable = [
        'site_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'twitter_card_title',
        'twitter_card_description',
        'site_address'
    ];
    protected $casts=[
        'site_name' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'meta_keywords' => 'array',
        'og_title' => 'array',
        'og_description' => 'array',
        'twitter_card_title' => 'array',
        'twitter_card_description' => 'array',
        'site_address'=>"site_adderss"
    ];
}
