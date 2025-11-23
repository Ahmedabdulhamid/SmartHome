<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class RedirectLivewire
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the request is a Livewire update request
        if ($request->header('X-Livewire') && str_contains($request->path(), 'livewire/update')) {
            // Get the referer (previous page URL) to determine the locale
            $referer = $request->header('referer');
            if ($referer) {
                $refererPath = parse_url($referer, PHP_URL_PATH);
                $locale = explode('/', $refererPath)[1] ?? null;

                // Check if the locale is supported and not present in the current path
                if ($locale && in_array($locale, LaravelLocalization::getSupportedLanguagesKeys()) && !str_contains($request->path(), $locale)) {
                    $path = LaravelLocalization::getLocalizedURL($locale, '/livewire/update');
                    return redirect($path);
                }
            }
        }
        return $next($request);
    }
}
