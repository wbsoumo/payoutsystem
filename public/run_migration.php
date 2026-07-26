<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/
require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Bootstrap Laravel
|--------------------------------------------------------------------------
*/
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');
echo "Novexapay Programmatic Database Migration Runner\n";
echo "=================================================\n\n";

try {
    echo "Resolving Laravel Migrator...\n";
    $migrator = app('migrator');
    
    if (!$migrator->repositoryExists()) {
        echo "Creating migrations repository table...\n";
        $migrator->getRepository()->createRepository();
    }
    
    echo "Running pending database migrations...\n";
    // Run migrations programmatically (bypassing Termwind/DOMDocument console output)
    $migrated = $migrator->run(database_path('migrations'));
    
    if (empty($migrated)) {
        echo "Nothing to migrate (Database is up to date).\n";
    } else {
        echo "Successfully migrated:\n";
        foreach ($migrated as $file) {
            echo " - " . basename($file) . "\n";
        }
    }
    
    echo "\nClearing config cache programmatically...\n";
    $configCache = base_path('bootstrap/cache/config.php');
    if (file_exists($configCache)) {
        if (@unlink($configCache)) {
            echo "Config cache file deleted successfully.\n";
        } else {
            echo "Warning: Failed to delete config cache file.\n";
        }
    } else {
        echo "No cached config file found (already clear).\n";
    }
    
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
}
