<?php

namespace App\Providers\Loaders;

use Illuminate\Support\Facades\File;

class _Loader
{
    /**
     * Generic method to load resources from bundles.
     *
     * This method iterates through all bundle directories and applies a callback
     * to each resource file or directory found within the specified sub-path.
     */
    public function __invoke(
        string $subPath,
        callable $callback,
        bool $isDirectory = false
    ): void {
        // Define the base path for bundles
        $bundlePath = base_path('app/Bundle');
        // Get all directories in the bundle path
        $bundleDirectories = $this->getAllDirectories(path: $bundlePath);

        // Iterate through each bundle directory
        foreach ($bundleDirectories as $bundleDirectory) {
            $targetPath = $bundleDirectory.$subPath;

            // If the target path exists
            if (File::isDirectory($targetPath)) {
                if ($isDirectory) {
                    // If we're looking for directories, call the callback with the directory
                    $callback(
                        $targetPath,
                        $bundleDirectory
                    );
                } else {
                    // If we're looking for files, get all files and call the callback for each
                    $filesOrDirectories = File::allFiles($targetPath);
                    foreach ($filesOrDirectories as $fileOrDirectory) {
                        $callback(
                            $fileOrDirectory,
                            $bundleDirectory
                        );
                    }
                }
            }
        }
    }

    /**
     * Recursively get all directories.
     *
     * This method returns an array of all subdirectories within the given path.
     */
    private function getAllDirectories(string $path): array
    {
        $directories = [];
        // Get all immediate subdirectories
        $allItems = File::directories($path);

        foreach ($allItems as $allItem) {
            // Add the current directory
            $directories[] = $allItem;
            // Recursively get subdirectories
            $subDirectories = $this->getAllDirectories(path: $allItem);
            // Merge subdirectories into the main array
            $directories = array_merge(
                $directories,
                $subDirectories
            );
        }

        return $directories;
    }

    /**
     * Extract namespace from file contents.
     *
     * This method uses a regular expression to find the namespace declaration in the file contents.
     */
    public function getNamespaceFromContents($contents): ?string
    {
        // Use regex to find the namespace
        if (preg_match(
            '/namespace\s+(.+?);/',
            (string) $contents,
            $matches
        )) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Extract class name from file contents.
     *
     * This method uses a regular expression to find the class declaration in the file contents.
     */
    public function getClassNameFromContents($contents): ?string
    {
        // Use regex to find the class name
        if (preg_match(
            '/class\s+(\w+)/',
            (string) $contents,
            $matches
        )) {
            return $matches[1];
        }

        return null;
    }
}
