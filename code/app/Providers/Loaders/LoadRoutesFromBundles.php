<?php

namespace App\Providers\Loaders;

use Illuminate\Support\Facades\Route;

readonly class LoadRoutesFromBundles
{
    public function __construct(
        private _Loader $loader
    ) {}

    public function __invoke(): void
    {
        // Use the loadFromBundles method to process each bundle
        ($this->loader)(
            // Specify the sub-path where route files are located
            subPath: '/Infrastructure/Routes',
            // Define the callback to process each route file
            callback: function (
                $routeFile
            ): void {
                // Add the route file to the 'api' middleware group
                Route::middleware('api')->prefix('api')->group(callback: $routeFile->getPathname());
            }
        );
    }
}
