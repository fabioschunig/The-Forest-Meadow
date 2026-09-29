<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $prefix = $request->route('locale');

        App::setLocale(config('app.locales')[$prefix]);

        // Lets route() build localized URLs without passing the locale every time.
        URL::defaults(['locale' => $prefix]);

        return $next($request);
    }
}
