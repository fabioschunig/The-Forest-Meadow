<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentTimezone;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Facades\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentTimezone::set(config('app.display_timezone'));

        // Content is written in Portuguese first; untranslated fields fall back to it.
        Translatable::fallback(fallbackLocale: 'pt_BR');
    }
}
