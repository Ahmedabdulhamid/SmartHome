<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Currency;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Slider;
use App\Support\FrontendCache;
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
            $sharedData = FrontendCache::remember('shared_view_data', [
                'locale' => app()->getLocale(),
            ], 1800, function () {
                return [
                    'setting' => Setting::query()->first(),
                    'sliders' => Slider::query()->get(),
                    'pages' => Page::query()->get(),
                    'headerCategories' => Category::query()->has('products')->get(),
                    'headerCurrencies' => Currency::query()->has('products')->get(),
                ];
            });

            $view->with($sharedData);
        });
    }
}
