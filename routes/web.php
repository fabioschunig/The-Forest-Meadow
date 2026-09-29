<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $locales = config('app.locales');

    // Default locale goes first: it's what getPreferredLanguage() returns when
    // the browser asks for nothing we serve.
    $available = array_values(array_unique([config('app.locale'), ...array_values($locales)]));
    $preferred = $request->getPreferredLanguage($available);

    return redirect()
        ->route('home', ['locale' => array_search($preferred, $locales, true)])
        ->header('Vary', 'Accept-Language');
});

Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('app.locales')))
    ->middleware(SetLocale::class)
    ->group(function () {
        Route::view('/', 'home')->name('home');
    });
