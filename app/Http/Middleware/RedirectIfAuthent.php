<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $guards = empty($guards) ? [null] : $guards;
        $isAdminPanelPath = $request->is('admin') || $request->is('admin/*');

        if (Auth::guard('web')->check()) {

            if ($isAdminPanelPath) {

                return to_route('home');
            }
            return to_route('home');
        }

        return $next($request);
    }
}
