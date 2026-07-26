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
        string $apiSecret,
        string $clientIp,
        string $merchantId
    ): array {
        $apiKeyHash = hash('sha256', $apiKey);
        $keyRecord = MerchantApiKey::where('api_key_hash', $apiKeyHash)
            ->where('is_active', true)
            ->first();

        if (!$keyRecord) {
            return ['status' => false, 'code' => 401, 'message' => 'Invalid API Key', 'merchant_id' => null];
        }

        if (trim(strtolower((string)$keyRecord->merchant_id)) !== trim(strtolower((string)$merchantId))) {
            return ['status' => false, 'code' => 401, 'message' => 'API Key does not match the provided Merchant ID', 'merchant_id' => null];
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

        // Validate API Secret Key
        $actualSecret = $keyRecord->secret_key_encrypted; // Cast decrypts automatically
        if ($apiSecret !== $actualSecret) {
            return ['status' => false, 'code' => 401, 'message' => 'Invalid API Secret Key', 'merchant_id' => $merchant->id];
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
