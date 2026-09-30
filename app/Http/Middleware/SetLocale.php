<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Applies the locale of the route group's URL prefix (e.g. SetLocale:pt).
     */
    public function handle(Request $request, Closure $next, string $prefix): Response
    {
        App::setLocale(config('app.locales')[$prefix]);

        return $next($request);
    }
}
