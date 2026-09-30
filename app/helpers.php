<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

if (! function_exists('locale_prefix')) {
    /**
     * URL prefix (pt, en) of the active locale.
     */
    function locale_prefix(): string
    {
        $prefix = array_search(App::getLocale(), config('app.locales'), true);

        return $prefix === false ? array_key_first(config('app.locales')) : $prefix;
    }
}

if (! function_exists('localized_route')) {
    /**
     * Route URL in the active locale (or the given prefix): localized_route('projects.show', ['slug' => 'x']).
     */
    function localized_route(string $name, array $parameters = [], ?string $prefix = null): string
    {
        return route(($prefix ?? locale_prefix()).'.'.$name, $parameters);
    }
}

if (! function_exists('switch_locale_url')) {
    /**
     * The current page in another locale, falling back to that locale's home.
     */
    function switch_locale_url(string $prefix): string
    {
        $route = Route::current();
        $name = $route?->getName();

        if ($name === null || ! str_starts_with($name, locale_prefix().'.')) {
            return localized_route('home', prefix: $prefix);
        }

        $baseName = substr($name, strlen(locale_prefix()) + 1);

        return Route::has("{$prefix}.{$baseName}")
            ? localized_route($baseName, $route->parameters(), $prefix)
            : localized_route('home', prefix: $prefix);
    }
}

if (! function_exists('upload_url')) {
    /**
     * Public URL of a file on the uploads disk (covers, content images).
     */
    function upload_url(?string $path): ?string
    {
        return filled($path) ? Storage::disk('uploads')->url($path) : null;
    }
}
