<?php

namespace App\Providers\Loaders;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class LoadCommandsFromBundles extends ServiceProvider
{
    public function __construct(
        private readonly _Loader $loader,
        private readonly Application $application
    ) {
        parent::__construct($this->application);
    }

    /**
     * Load commands from all bundles.
     *
     * This method searches for command files in each bundle and registers them if the application is running in console.
     */
    public function __invoke(): void
    {
        // Initialize an array to hold command classes
        $commands = [];

        // Use the loadFromBundles method to process each bundle
        ($this->loader)(
            // Specify the sub-path where command files are located
            subPath: '/Infrastructure/Console/Commands',
            // Define the callback to process each command file
            callback: function (
                $commandFile
            ) use (
                &$commands
            ): void {
                // Extract the full namespace of the command class from the file
                $namespace = $this->getNamespaceFromFile(file: $commandFile);
                if ($namespace) {
                    // Add the command class to the array
                    $commands[] = $namespace;
                }
            }
        );

        // If running in console, register all found commands
        if ($this->application->runningInConsole()) {
            $this->commands(commands: $commands);
        }
    }

    /**
     * Get the namespace and class name from a file.
     *
     * This method extracts the full namespaced class name from a PHP file.
     */
    private function getNamespaceFromFile($file): ?string
    {
        // Get the contents of the file
        $content = File::get($file);
        // Extract the namespace
        $namespace = $this->loader->getNamespaceFromContents(contents: $content);
        // Extract the class name
        $class = $this->loader->getClassNameFromContents(contents: $content);

        // Return full namespaced class name if both namespace and class are found
        return $namespace && $class ? "$namespace\\$class" : null;
    }
}
