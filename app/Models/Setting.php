<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
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
    protected static function booted()
    {
        static::updated(function ($setting) {
            $original = $setting->getOriginal();

            // site_logo
            if (isset($original['site_logo']) && $original['site_logo'] !== $setting->site_logo) {
                if ($original['site_logo'] && Storage::disk('public')->exists($original['site_logo'])) {
                    Storage::disk('public')->delete($original['site_logo']);
                }
            }

            // favicon
            if (isset($original['favicon']) && $original['favicon'] !== $setting->favicon) {
                if ($original['favicon'] && Storage::disk('public')->exists($original['favicon'])) {
                    Storage::disk('public')->delete($original['favicon']);
                }
            }
        });
    }
}
