<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    // Required (متعدد اللغات)
    public array $site_name = ['en' => '', 'ar' => ''];

    // **🔥 الحل هنا: يجب أن تكون قابلة للـ Null**
    public ?string $site_email = '';
    public ?string $site_phone = '';

    // Nullable (وهي صحيحة في كلاسك)
    public ?string $site_logo = '';
    public ?string $favicon = '';

    public ?string $facebook_url = '';
    public ?string $twitter_url = '';
    public ?string $instagram_url = '';
    public ?string $linkedin_url = '';
    public ?string $youtube_url = '';

    // SEO
    public array $meta_title = ['en' => '', 'ar' => ''];
    public array $meta_description = ['en' => '', 'ar' => ''];
    public array $meta_keywords = ['en' => '', 'ar' => ''];

    public ?string $og_image = '';
    public array $og_title = ['en' => '', 'ar' => ''];
    public array $og_description = ['en' => '', 'ar' => ''];

    public ?string $twitter_card_image = '';
    public array $twitter_card_title = ['en' => '', 'ar' => ''];
    public array $twitter_card_description = ['en' => '', 'ar' => ''];

    public static function group(): string
    {
        return 'general';
    }
}
