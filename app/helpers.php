<?php

use Illuminate\Database\Eloquent\Model;
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

        // Only URL parameters: Route::view() also carries "view" and "status" as parameters.
        $parameters = array_intersect_key($route->parameters(), array_flip($route->parameterNames()));

        return Route::has("{$prefix}.{$baseName}")
            ? localized_route($baseName, $parameters, $prefix)
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

if (! function_exists('default_locale_prefix')) {
    /**
     * URL prefix of the default locale: the first entry of config('app.locales').
     * Not config('app.locale'), which App::setLocale() rewrites on every /en request.
     */
    function default_locale_prefix(): string
    {
        return array_key_first(config('app.locales'));
    }
}

if (! function_exists('default_locale')) {
    /**
     * The locale content is written in first (pt_BR).
     */
    function default_locale(): string
    {
        return config('app.locales')[default_locale_prefix()];
    }
}

if (! function_exists('page_alternates')) {
    /**
     * URLs of the current page in each locale where it exists: every locale for
     * fixed pages, only translated locales for a project or note.
     *
     * @return array<string, string> URL prefix => URL
     */
    function page_alternates(?Model $model = null): array
    {
        $alternates = [];

        foreach (config('app.locales') as $prefix => $locale) {
            $available = $model === null
                || $prefix === default_locale_prefix()
                || $model->hasTranslation('title', $locale);

            if ($available) {
                $alternates[$prefix] = switch_locale_url($prefix);
            }
        }

        return $alternates;
    }
}
