package main

import (
	"fmt"
	"io/ioutil"
	"os"
	"path/filepath"
	"strings"
	"time"
)

const (
	// Define the destination folder
	destinationFolder = "docker_app"

	// Define the content folder
	baseProjectDir = "/var/www/.docker"

	// Define the generated file prefix
	generatedFilePrefix = "DOCKER_"

	// Define file extensions to include (use "*" for any extension, ".php" for PHP files, ".go" for Go files)
	fileExtensions = "*" // Change to "*" for any extension or ".php" for PHP files
)

func main() {
	// Print the current directory path and destination folder
	fmt.Println("Base directory:", baseProjectDir)
	fmt.Println("Destination folder:", destinationFolder)

	// Get the current date and time for the timestamp
	timestamp := time.Now().Format("20060102_150405")

	// File name with timestamp
	outputFileName := fmt.Sprintf("_%s_output_%s.md", generatedFilePrefix, timestamp)

	// Ensure the destination folder exists
	err := os.MkdirAll(destinationFolder, os.ModePerm)
	if err != nil {
		fmt.Println("Error creating the destination folder:", err)
		return
	}

	// Full path for the output file
	outputFilePath := filepath.Join(destinationFolder, outputFileName)

	// Create the output file
	outputFile, err := os.Create(outputFilePath)
	if err != nil {
		fmt.Println("Error creating the output file:", err)
		return
	}
	defer outputFile.Close()

	// Folders to ignore
	ignoreFolders := []string{".idea", ".git", "db", "vendor", "views", "config", "cache"}

	// Recursively walk through the current directory
	err = filepath.Walk(baseProjectDir, func(path string, info os.FileInfo, err error) error {
		if err != nil {
			fmt.Printf("Error accessing path %s: %v\n", path, err)
			return err
		}

		// Print the current path and info
		fmt.Printf("Visiting path: %s\n", path)

		// Ignore hidden folders and files and the specified folders
		if info.IsDir() && (contains(ignoreFolders, info.Name())) {
			fmt.Printf("Ignoring directory: %s\n", path)
			return filepath.SkipDir
		}

        if !info.IsDir() && strings.HasSuffix(info.Name(), ".md") {
            fmt.Printf("Skipping markdown file: %s\n", path)
            return nil
        }

		// Check if the file matches the extension criteria
		if !info.IsDir() && matchesExtension(path) {
			fmt.Printf("Processing file: %s\n", path)
			// Read the file content
			content, err := ioutil.ReadFile(path)
			if err != nil {
				fmt.Printf("Error reading file %s: %v\n", path, err)
				return err
			}

			// Calculate the relative path from the base path to the file
			relativePath, err := filepath.Rel(baseProjectDir, path)
			if err != nil {
				fmt.Printf("Error calculating relative path for file %s: %v\n", path, err)
				return err
			}

			// Write the name, path, and content to the output file
			_, err = outputFile.WriteString(fmt.Sprintf("# File path: %s\n## File content:\n```\n%s\n```\n\n\n", relativePath, string(content)))
			if err != nil {
				fmt.Printf("Error writing to output file: %v\n", err)
				return err
			}
		}

		return nil
	})

	if err != nil {
		fmt.Println("Error walking through the path:", err)
	}
}

// Check if the file matches the defined extension criteria
func matchesExtension(path string) bool {
	if fileExtensions == "*" {
		return true
	}
	return strings.HasSuffix(path, fileExtensions)
}

// Helper function to check if a slice contains a specific string
func contains(slice []string, str string) bool {
	for _, s := range slice {
		if s == str {
			return true
		}
	}
	return false
}
