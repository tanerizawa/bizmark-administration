<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

class NavCountObserver
{
    /**
     * Handle the "created" event.
     *
     * @param  mixed  $model
     */
    public function created($model): void
    {
        $this->clearCache();
    }

    /**
     * Handle the "deleted" event.
     *
     * @param  mixed  $model
     */
    public function deleted($model): void
    {
        $this->clearCache();
    }

    /**
     * Handle the "restored" event.
     *
     * @param  mixed  $model
     */
    public function restored($model): void
    {
        $this->clearCache();
    }

    /**
     * Handle the "force deleted" event.
     *
     * @param  mixed  $model
     */
    public function forceDeleted($model): void
    {
        $this->clearCache();
    }

    /**
     * Clear navigation counts cache.
     */
    protected function clearCache(): void
    {
        Cache::forget('nav_counts');
    }
}
