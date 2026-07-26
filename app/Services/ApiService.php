<?php

namespace App\Services;

use App\Models\MerchantApiKey;
use App\Models\MerchantIpWhitelist;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

class ApiService
{
    public function generateKeys(string $merchantId, string $name = 'Default Key'): array
    {
        $apiKey = 'nvx_pk_live_' . Str::random(32);
        $secretKey = 'nvx_sk_live_' . Str::random(48);
        $webhookSecret = 'whsec_' . Str::random(32);

        $apiKeyHash = hash('sha256', $apiKey);
        $apiKeyPreview = substr($apiKey, 0, 16) . '...' . substr($apiKey, -4);

        // Deactivate old keys
        MerchantApiKey::where('merchant_id', $merchantId)->update(['is_active' => false]);

        $keyRecord = MerchantApiKey::create([
            'merchant_id' => $merchantId,
            'name' => $name,
            'api_key_hash' => $apiKeyHash,
            'api_key_preview' => $apiKeyPreview,
            'secret_key_encrypted' => $secretKey, // Cast handles encrypt
            'webhook_secret_encrypted' => $webhookSecret, // Cast handles encrypt
            'is_active' => true,
        ]);

        return [
            'api_key' => $apiKey,
            'secret_key' => $secretKey,
            'webhook_secret' => $webhookSecret,
            'record' => $keyRecord,
        ];
    }

    public function validateRequest(
        string $apiKey,
        string $signature,
        string $timestamp,
        string $nonce,
        string $requestBody,
        string $clientIp
    ): array {
        $apiKeyHash = hash('sha256', $apiKey);
        $keyRecord = MerchantApiKey::where('api_key_hash', $apiKeyHash)
            ->where('is_active', true)
            ->first();

        if (!$keyRecord) {
            return ['status' => false, 'code' => 401, 'message' => 'Invalid API Key', 'merchant_id' => null];
        }

        $merchant = $keyRecord->merchant;
        if (!$merchant || $merchant->status !== 'active') {
            return ['status' => false, 'code' => 403, 'message' => 'Merchant account is not active', 'merchant_id' => null];
        }

        // IP Whitelisting Check
        $whitelistedIps = MerchantIpWhitelist::where('merchant_id', $merchant->id)
            ->where('is_active', true)
            ->pluck('ip_address')
            ->toArray();

        if (!empty($whitelistedIps)) {
            if (!in_array($clientIp, $whitelistedIps)) {
                return ['status' => false, 'code' => 403, 'message' => "IP Address {$clientIp} is not whitelisted.", 'merchant_id' => $merchant->id];
            }
        }

        // Timestamp Validation (5 minutes window)
        $diff = abs(time() - (int) $timestamp);
        if ($diff > 300) {
            return ['status' => false, 'code' => 400, 'message' => 'Request expired (timestamp out of window)', 'merchant_id' => $merchant->id];
        }

        // Nonce Validation (Replay Protection)
        $cacheKey = "nonce:{$merchant->id}:{$nonce}";
        if (Cache::has($cacheKey)) {
            return ['status' => false, 'code' => 400, 'message' => 'Replay attack detected (nonce reused)', 'merchant_id' => $merchant->id];
        }
        Cache::put($cacheKey, true, 300); // cache for 5 minutes

        // Signature Validation (HMAC-SHA256)
        $secretKey = $keyRecord->secret_key_encrypted; // Cast decrypts automatically
        $stringToSign = $timestamp . '.' . $nonce . '.' . $requestBody;
        $expectedSignature = hash_hmac('sha256', $stringToSign, $secretKey);

        if (!hash_equals($expectedSignature, $signature)) {
            return ['status' => false, 'code' => 401, 'message' => 'Invalid signature', 'merchant_id' => $merchant->id];
        }

        return [
            'status' => true,
            'code' => 200,
            'message' => 'Validation successful',
            'merchant_id' => $merchant->id,
            'merchant' => $merchant,
        ];
    }
}
