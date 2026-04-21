<?php

namespace App\Modules\Shared\Providers;

use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    protected string $moduleName = '';
    protected string $moduleNamespace = '';

    public function register(): void
    {
        $this->registerConfig();
    }

    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadMigrations();
        $this->registerObservers();
    }

    protected function registerConfig(): void {}

    protected function loadRoutes(): void
    {
        $routeFile = $this->getModulePath('routes.php');
        if (file_exists($routeFile)) {
            $this->loadRoutesFrom($routeFile);
        }
    }

    protected function loadMigrations(): void
    {
        $migrationsPath = $this->getModulePath('database/migrations');
        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    protected function registerObservers(): void {}

    protected function getModulePath(string $path = ''): string
    {
        $base = dirname((new \ReflectionClass(static::class))->getFileName(), 2);
        return $path ? $base . '/' . $path : $base;
    }
}
