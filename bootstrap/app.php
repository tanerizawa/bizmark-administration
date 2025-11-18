<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/mobile.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add SetLocale middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        
        // Register middleware aliases
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'mobile' => \App\Http\Middleware\DetectMobile::class,
        ]);
        
        // Exclude webhook endpoints from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
