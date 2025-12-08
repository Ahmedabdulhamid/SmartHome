<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyAdminActions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->guard('web')->user()&& auth()->guard('web')->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'You are not allowed to perform this action.');
        }

        return $next($request);
    }
}
