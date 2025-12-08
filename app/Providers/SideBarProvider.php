<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SideBarProvider extends ServiceProvider
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
        $clientsCount = \App\Models\Client::count();
        $invoicesCount = \App\Models\Invoice::count();
        view()->share('clientsCount', $clientsCount);
        view()->share('invoicesCount', $invoicesCount);
    }
}
