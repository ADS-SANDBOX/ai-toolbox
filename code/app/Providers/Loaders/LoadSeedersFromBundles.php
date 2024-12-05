<?php

namespace App\Providers\Loaders;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Seeder;

readonly class LoadSeedersFromBundles
{
    public function __construct(
        private _Loader $loader,
        private Application $application
    ) {}

    public function __invoke(): void
    {
        $seeders = [];

        ($this->loader)(
            subPath: '/Infrastructure/Database/Seeders',
            callback: function ($seederFile) use (&$seeders): void {
                $seederClass = $this->getClassFromFile(file: $seederFile);
                if ($seederClass && is_subclass_of($seederClass, Seeder::class)) {
                    $seeders[] = $seederClass;
                }
            }
        );

        $this->application->singleton(abstract: 'bundle.seeders', concrete: fn (): array => $seeders);

        $this->application->singleton(abstract: 'seeder', concrete: fn () => $this->application->make(abstract: DatabaseSeeder::class));
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
