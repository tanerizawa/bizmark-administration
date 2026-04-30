<?php

namespace App\Providers;

use App\View\Composers\NavigationComposer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        File::ensureDirectoryExists((string) config('view.compiled'));

        // Force HTTPS in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Share navigation counts to layout (with caching)
        View::composer('layouts.app', NavigationComposer::class);

        // Share mobile layout data
        View::composer('mobile.layouts.app', \App\Http\View\Composers\MobileLayoutComposer::class);

        // Note: All model observers are registered via #[ObservedBy] attribute
        // on each model class (Laravel 10+ best practice).
    }
}
