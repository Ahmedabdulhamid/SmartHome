<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Settings\GeneralSettings;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(GeneralSettings::class);

        $settings->site_name = ['en' => 'My Site', 'ar' => 'موقعي'];
        $settings->site_email = 'info@example.com';
        $settings->site_phone = '+20123456789';

        $settings->site_logo = '';
        $settings->favicon = '';
        $settings->facebook_url = '';
        $settings->twitter_url = '';
        $settings->instagram_url = '';
        $settings->linkedin_url = '';
        $settings->youtube_url = '';

        $settings->meta_title = ['en' => '', 'ar' => ''];
        $settings->meta_description = ['en' => '', 'ar' => ''];
        $settings->meta_keywords = ['en' => '', 'ar' => ''];

        $settings->og_image = '';
        $settings->og_title = ['en' => '', 'ar' => ''];
        $settings->og_description = ['en' => '', 'ar' => ''];

        $settings->twitter_card_image = '';
        $settings->twitter_card_title = ['en' => '', 'ar' => ''];
        $settings->twitter_card_description = ['en' => '', 'ar' => ''];

        $settings->save();
    }
}
