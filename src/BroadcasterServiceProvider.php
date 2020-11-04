<?php

declare(strict_types=1);

namespace Voice\EloquentEventBroadcaster;

use Illuminate\Support\ServiceProvider;

class BroadcasterServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/asseco-broadcaster.php', 'asseco-broadcaster');
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishes([__DIR__ . '/../config/asseco-broadcaster.php' => config_path('asseco-broadcaster.php')]);
    }
}
