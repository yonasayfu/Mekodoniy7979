<?php

namespace App\Providers;

use App\Services\MessageService;
use Illuminate\Support\ServiceProvider;

class OutboundServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(MessageService::class, function ($app) {
            return new MessageService();
        });

        $this->mergeConfigFrom(
            __DIR__.'/../../config/outbound.php', 'outbound'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/outbound.php' => config_path('outbound.php'),
        ], 'config');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\OutboundStatusCommand::class,
                \App\Console\Commands\RetryFailedOutboundMessages::class,
                \App\Console\Commands\CleanupOutboundMessages::class,
            ]);
        }
    }
}
