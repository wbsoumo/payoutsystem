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
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Force run migrations
header('Content-Type: text/plain');
echo "Novexapay cPanel Staging Migration Runner\n";
echo "=========================================\n\n";

try {
    echo "Executing: artisan migrate --force ...\n";
    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "Exit Code: " . $exitCode . "\n";
    echo "Output:\n" . \Illuminate\Support\Facades\Artisan::output() . "\n";
    
    echo "Executing: artisan config:clear ...\n";
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "Config cleared successfully.\n";
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
