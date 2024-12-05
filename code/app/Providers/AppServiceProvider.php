<?php

declare(strict_types=1);

namespace App\Providers;

use App\Providers\Loaders\LoadCommandsFromBundles;
use App\Providers\Loaders\LoadMigrationsFromBundles;
use App\Providers\Loaders\LoadRoutesFromBundles;
use App\Providers\Loaders\LoadSeedersFromBundles;
use App\Providers\Loaders\LoadServiceProvidersFromBundles;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider.
 *
 * This service provider is responsible for bootstrapping application services,
 * including loading various resources from bundles such as routes, views,
 * migrations, commands, service providers, and Blade components.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * This method is called after all other service providers have been registered.
     * It configures SSL and loads various bundle resources.
     */
    public function boot(): void
    {
        // Configure SSL if enabled in the environment
        $this->configureSSL();

        // Load various resources from bundles
        // Load routes from all bundles
        app(LoadRoutesFromBundles::class)();

        // Load migrations from all bundles (currently commented out)
        app(LoadMigrationsFromBundles::class)();
        app(LoadSeedersFromBundles::class)();

        // Load commands from all bundles
        app(LoadCommandsFromBundles::class)();

        // Load service providers from all bundles
        app(LoadServiceProvidersFromBundles::class)();

        // Helpers
        $helperPath = app_path('Bundle/_Shared/Platform/Infrastructure/Helpers/Autoload.php');

        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    /**
     * Configure SSL if enabled in the environment.
     *
     * This method forces HTTPS if APP_SSL is set to true in the environment.
     */
    private function configureSSL(): void
    {
        // Check if APP_SSL is set to true in the environment
        if (env('APP_SSL') === true) {
            // Force HTTPS scheme for all URLs
            URL::forceScheme('https');
            $this->app['request']->server->set(
                'HTTPS',
                'on'
            );
        }
    }
}
