<?php

namespace App\Providers\Loaders;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

readonly class LoadServiceProvidersFromBundles
{
    public function __construct(
        private _Loader $loader,
        private Application $application
    ) {}

    /**
     * Load service providers from all bundles.
     *
     * This method searches for service provider files in each bundle and registers them.
     */
    public function __invoke(): void
    {
        // Use the loadFromBundles method to process each bundle
        ($this->loader)(
            // Specify the sub-path where service provider files are located
            subPath: '/Infrastructure/Providers',
            // Define the callback to process each service provider file
            callback: function (
                $providerFile
            ): void {
                // Extract the full class name of the provider from the file
                $providerClass = $this->getClassFromFile(file: $providerFile);
                // If the class is a subclass of ServiceProvider, register it
                if ($providerClass && is_subclass_of(
                    $providerClass,
                    ServiceProvider::class
                )) {
                    $this->application->register(provider: $providerClass);
                }
            }
        );
    }

    /**
     * Get the full class name from a file.
     *
     * This method is similar to getNamespaceFromFile but uses file_get_contents instead of File::get.
     */
    private function getClassFromFile($file): ?string
    {
        // Get the contents of the file
        $contents = file_get_contents($file->getPathname());
        // Extract the namespace
        $namespace = $this->loader->getNamespaceFromContents(contents: $contents);
        // Extract the class name
        $class = $this->loader->getClassNameFromContents(contents: $contents);

        // Return full namespaced class name if both namespace and class are found
        return $namespace && $class ? $namespace.'\\'.$class : null;
    }
}
