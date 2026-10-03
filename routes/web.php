<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Feed\Http\FeedController;

Route::get('/', function (Request $request) {
    $locales = config('app.locales');

    // Default locale goes first: it's what getPreferredLanguage() returns when
    // the browser asks for nothing we serve.
    $available = array_values(array_unique([default_locale(), ...array_values($locales)]));
    $preferred = $request->getPreferredLanguage($available);

    return redirect()
        ->route(array_search($preferred, $locales, true).'.home')
        ->header('Vary', 'Accept-Language');
});

Route::get('sitemap.xml', SitemapController::class)->name('sitemap');

// Replaces public/robots.txt, which would win over this route: the sitemap needs an absolute URL.
Route::get('robots.txt', fn () => response(
    "User-agent: *\nDisallow: /admin\n\nSitemap: ".route('sitemap')."\n",
)->header('Content-Type', 'text/plain; charset=UTF-8'))->name('robots');

// One route group per locale, with URL segments translated from lang/*/routes.php.
foreach (config('app.locales') as $prefix => $locale) {
    $segment = fn (string $key): string => trans("routes.{$key}", [], $locale);

    Route::prefix($prefix)
        ->name("{$prefix}.")
        ->middleware(SetLocale::class.":{$prefix}")
        ->group(function () use ($segment, $prefix) {
            Route::get('/', HomeController::class)->name('home');
            Route::get($segment('projects'), [ProjectController::class, 'index'])->name('projects.index');
            Route::get($segment('projects').'/{slug}', [ProjectController::class, 'show'])->name('projects.show');
            Route::get($segment('notes'), [NoteController::class, 'index'])->name('notes.index');
            // Before notes/{slug}, or "feed" would be read as a slug. The package's
            // controller picks the feed config from whatever follows "feeds." in the name.
            Route::get($segment('notes').'/feed', FeedController::class)->name("feeds.{$prefix}");
            Route::get($segment('notes').'/{slug}', [NoteController::class, 'show'])->name('notes.show');
            Route::view($segment('about'), 'pages.about')->name('about');

            // Unknown URLs inside a locale still get a 404 page in that locale.
            Route::fallback(fn () => abort(404))->name('fallback');
        });
}
