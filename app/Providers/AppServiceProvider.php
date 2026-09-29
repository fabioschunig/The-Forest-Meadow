<?php

namespace App\Providers;

use Filament\Forms\Components\FileUpload;
use Filament\Support\Facades\FilamentTimezone;
use Filament\Tables\Columns\ImageColumn;
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

        FileUpload::configureUsing(fn (FileUpload $upload) => $upload->disk('uploads')->visibility('public'));
        ImageColumn::configureUsing(fn (ImageColumn $column) => $column->disk('uploads')->visibility('public'));

        // Content is written in Portuguese first; untranslated fields fall back to it.
        Translatable::fallback(fallbackLocale: 'pt_BR');
    }
}
