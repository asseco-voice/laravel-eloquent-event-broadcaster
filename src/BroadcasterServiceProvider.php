<?php

declare(strict_types=1);

namespace Asseco\EloquentEventBroadcaster;

use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider;
use Psr\Log\NullLogger;

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
        $this->publishes([
            __DIR__ . '/../config/asseco-broadcaster.php' => config_path('asseco-broadcaster.php'),
        ], 'asseco-broadcaster-config');

        app()->singleton('broadcasterLog', function ($app) {
            return config('asseco-broadcaster.enable_logs') ? new LogManager($app) : new NullLogger();
        });
    }
}
