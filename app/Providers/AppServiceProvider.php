<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use App\View\Composers\NavigationComposer;

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

        // Register observers to invalidate nav counts cache
        \App\Models\Project::observe(\App\Observers\NavCountObserver::class);
        \App\Models\Task::observe(\App\Observers\NavCountObserver::class);
        \App\Models\Document::observe(\App\Observers\NavCountObserver::class);
        \App\Models\Institution::observe(\App\Observers\NavCountObserver::class);
        \App\Models\CashAccount::observe(\App\Observers\NavCountObserver::class);

        // Register notification observers for real-time notifications
        \App\Models\Task::observe(\App\Observers\TaskObserver::class);
        \App\Models\Document::observe(\App\Observers\DocumentObserver::class);
        \App\Models\PermitApplication::observe(\App\Observers\PermitApplicationObserver::class);
        
        // Register ProjectObserver for auto-status and progress logic
        \App\Models\Project::observe(\App\Observers\ProjectObserver::class);

        $auditObserver = \App\Observers\AdminAuditObserver::class;
        \App\Models\User::observe($auditObserver);
        \App\Models\Role::observe($auditObserver);
        \App\Models\Permission::observe($auditObserver);
        \App\Models\BusinessSetting::observe($auditObserver);
        \App\Models\SecuritySetting::observe($auditObserver);
        \App\Models\PermitApplication::observe($auditObserver);
        \App\Models\Quotation::observe($auditObserver);
        \App\Models\Payment::observe($auditObserver);
        \App\Models\Project::observe($auditObserver);
        \App\Models\Task::observe($auditObserver);
    }
}
