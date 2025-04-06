<?php

namespace Install;

class Install
{
    public static function createDirectoryStructure()
    {
        // From vendor/odevnet/dulceauth/install to the root of the project
        $projectPath = realpath(__DIR__ . '/../../../../');
        $baseOrigin = realpath(__DIR__); // /install folder

        // Directories to create in the project
        $directories = ['config', 'logs'];

        foreach ($directories as $directory) {
            $path = $projectPath . DIRECTORY_SEPARATOR . $directory;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
                echo "Directory created: $path\n";
            } else {
                echo "Directory already exists: $path\n";
            }
        }

        // Files to copy from /install to the project root
        $files = [
            'config.php' => 'config/config.php',
            'config-db.php' => 'config/config-db.php',
            'verification_email.json' => 'config/verification_email.json',
            'forgot_password_email.json' => 'config/forgot_password_email.json',
            'log_file.log' => 'logs/log_file.log',
        ];

        foreach ($files as $sourceFile => $relativeDestinationPath) {
            $origin = $baseOrigin . DIRECTORY_SEPARATOR . $sourceFile;
            $destination = $projectPath . DIRECTORY_SEPARATOR . $relativeDestinationPath;

            if (!file_exists($origin)) {
                echo "File not found: $origin\n";
                continue;
            }

            if (!file_exists($destination)) {
                if (!copy($origin, $destination)) {
                    echo "Error copying $origin to $destination\n";
                } else {
                    echo "Copied: $origin => $destination\n";
                }
            } else {
                echo "Already exists: $destination\n";
            }
        }

        // Delete the current directory and its contents
        $currentPath = __DIR__;
        self::deleteDirectory($currentPath);
    }

    /**
     * Recursively deletes a directory and its contents.
     *
     * @param string $path The path of the directory to delete.
     */
    private static function deleteDirectory($path)
    {
        if (!is_dir($path)) {
            echo "Directory does not exist: $path\n";
            return;
        }

        $items = array_diff(scandir($path), ['.', '..']);

        foreach ($items as $item) {
            $itemPath = $path . DIRECTORY_SEPARATOR . $item;

            if (is_dir($itemPath)) {
                self::deleteDirectory($itemPath); // Recursively delete subdirectories
            } else {
                unlink($itemPath); // Delete file
                echo "File deleted: $itemPath\n";
            }
        }

        rmdir($path); // Delete the directory itself
        echo "Directory deleted: $path\n";
    }
}

// Install::createDirectoryStructure();
