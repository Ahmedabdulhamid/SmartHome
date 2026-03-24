<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\QuotationAdditionalCost;
use App\Models\QuotationItem;
use App\Observers\OrderObserver;
use App\Observers\QuotationAdditionalCostObserver;
use App\Observers\QuotationItemObserver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        QuotationItem::observe(QuotationItemObserver::class);

        Order::observe(OrderObserver::class);
        QuotationAdditionalCost::observe(QuotationAdditionalCostObserver::class);
        $ca = 'C:/php84/cacert.pem'; // أو حطه داخل المشروع واستخدم base_path('cacert.pem')

        if (is_file($ca)) {
            // ده اللي بيأثر على cURL/Guzzle على مستوى PHP كله
            ini_set('curl.cainfo', $ca);
            ini_set('openssl.cafile', $ca);

            // وده للـ Laravel Http Client
            Http::globalOptions(['verify' => $ca]);
        }
        RateLimiter::for('ai', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
