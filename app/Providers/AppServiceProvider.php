<?php

namespace App\Providers;

use App\Models\QuotationAdditionalCost;
use App\Models\QuotationItem;
use App\Observers\QuotationAdditionalCostObserver;
use App\Observers\QuotationItemObserver;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;

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
        QuotationAdditionalCost::observe(QuotationAdditionalCostObserver::class);

    }
}
