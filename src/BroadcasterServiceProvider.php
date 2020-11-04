<?php

declare(strict_types=1);

namespace Voice\EloquentEventBroadcaster;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Voice\Chassis\App\Console\Commands\EnvReplacerCommand;
use Voice\Chassis\App\Console\Commands\InitChassis;
use Voice\Chassis\App\Http\Middleware\EndQueryLogMiddleware;
use Voice\Chassis\App\Http\Middleware\ForceJsonHeaderMiddleware;
use Voice\Chassis\App\Http\Middleware\LogRequests;
use Voice\Chassis\App\Http\Middleware\StartQueryLogMiddleware;

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
