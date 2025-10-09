<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\NavCountComposer;

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
        // Share navigation counts to layout
        View::composer('layouts.app', NavCountComposer::class);

        // Register observers to invalidate nav counts cache
        \App\Models\Project::observe(\App\Observers\NavCountObserver::class);
        \App\Models\Task::observe(\App\Observers\NavCountObserver::class);
        \App\Models\Document::observe(\App\Observers\NavCountObserver::class);
        \App\Models\Institution::observe(\App\Observers\NavCountObserver::class);
        \App\Models\CashAccount::observe(\App\Observers\NavCountObserver::class);
    }
}
