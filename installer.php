<?php
// installer.php

require_once __DIR__ . '/install/Install.php';

use Install\Install;

echo "Running manual installation of dulceAuth...\n";

try {
    Install::createDirectoryStructure();
    echo "\n Installation completed successfully.\n";
} catch (Throwable $e) {
    echo "\n Error during installation: " . $e->getMessage() . "\n";
    exit(1);
}
