<?php
use Illuminate\Contracts\Console\Kernel;
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

header('Content-Type: text/plain');

try {
    $merchantId = '019f9dc9-a3aa-7076-b865-1f4ca42e790c';
    $merchant = \App\Models\Merchant::find($merchantId);
    if (!$merchant) {
        echo "Merchant not found\n";
        exit;
    }

    echo "=== Merchant Details ===\n";
    echo "ID: " . $merchant->id . "\n";
    echo "Name: " . $merchant->business_name . "\n";

    echo "\n=== Whitelisted IPs in DB ===\n";
    $whitelists = \App\Models\MerchantIpWhitelist::where('merchant_id', $merchant->id)->get();
    foreach ($whitelists as $w) {
        echo "IP: {$w->ip_address} | Active: " . ($w->is_active ? 'Yes' : 'No') . "\n";
    }

    echo "\n=== Detected client IP by PHP ===\n";
    echo "REMOTE_ADDR: " . ($_SERVER['REMOTE_ADDR'] ?? 'not set') . "\n";
    echo "HTTP_X_FORWARDED_FOR: " . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'not set') . "\n";
    echo "HTTP_CF_CONNECTING_IP: " . ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? 'not set') . "\n";
    echo "Request::ip(): " . request()->ip() . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
