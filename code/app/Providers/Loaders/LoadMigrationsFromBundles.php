<?php

namespace App\Providers\Loaders;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class LoadMigrationsFromBundles extends ServiceProvider
{
    public function __construct(
        private readonly _Loader $loader,
        private readonly Application $application
    ) {
        parent::__construct($this->application);
    }

    /**
     * Load migrations from all bundles.
     *
     * This method is currently commented out, but when active, it would search for
     * migration files in each bundle and load them.
     */
    public function __invoke(): void
    {
        $migrationPaths = [];

        ($this->loader)(
            subPath: '/Infrastructure/Database/Migrations',
            callback: function ($item) use (&$migrationPaths): void {
                if (is_dir($item)) {
                    $migrationPaths[] = $item;
                }
            },
            isDirectory: true
        );

        $migrationPaths = array_unique($migrationPaths);

        foreach ($migrationPaths as $migrationPath) {
            $this->loadMigrationsFrom(paths: $migrationPath);
        }
    }

    /**
     * Register database migration paths.
     *
     * @param  array|string  $paths
     */
    protected function loadMigrationsFrom($paths): void
    {
        $this->callAfterResolving(name: 'migrator', callback: function ($migrator) use ($paths): void {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }
}
