<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Currency;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Slider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class VisitorProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
           $setting = Setting::first();
            $sliders=Slider::all();
            $pages=Page::all();
            $view->with(['setting'=>$setting,'sliders'=>$sliders,'pages'=>$pages]);




        });

    }
}
