<?php

use App\Http\Middleware\RedirectIfAuthent;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RedirectLivewire;
use App\Http\Middleware\SetLocaleWeb;
use App\Http\Middleware\VerifyCsrfToken;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
          SetLocaleWeb::class,
          VerifyCsrfToken::class,

        ]);
        $middleware->alias([
            'guest2'=>RedirectIfAuthent::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
